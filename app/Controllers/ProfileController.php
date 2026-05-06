<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\City;
use App\Models\CV;
use App\Models\Country;
use App\Models\User;
use App\Services\GoogleCloudStorage;
use finfo;
use Throwable;

class ProfileController extends Controller
{
    public function show(): void
    {
        if (! isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        $userModel = new User();
        $user = $userModel->find((int) $_SESSION['user']['id']);

        if ($user === null) {
            $this->redirect('/logout');
        }

        $roleName = $userModel->getRole((int) $user['id'])['name'] ?? $_SESSION['user']['role'] ?? null;
        $user['role'] = $roleName;
        $_SESSION['user']['role'] = $roleName;

        $cvModel = new CV();
        $cv = $cvModel->findByUserId((int) $user['id']);
        $fullCv = $cv === null ? null : $cvModel->findFullCV((int) $cv['id']);

        $this->view('profile/show', [
            'title' => 'Profile',
            'user' => $user,
            'avatarUrl' => $user['avatar_url'] ?? null,
            'cv' => $fullCv,
            'headline' => $this->profileHeadline($fullCv, $roleName),
            'profileCompletion' => $this->profileCompletion($fullCv),
            'countries' => (new Country())->all('name'),
            'cities' => (new City())->all('name'),
        ]);
    }

    public function update(): void
    {
        if (! isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        $userId = (int) $_SESSION['user']['id'];
        $data = $this->only(['full_name', 'email', 'phone_number', 'country_id', 'city_id', 'street_address']);
        $errors = $this->validateProfile($data, $userId);
        $payload = [
            'full_name' => trim((string) ($data['full_name'] ?? '')),
            'email' => strtolower(trim((string) ($data['email'] ?? ''))),
        ];
        $cvPayload = [
            'full_name' => $payload['full_name'],
            'email' => $payload['email'],
            'phone_number' => trim((string) ($data['phone_number'] ?? '')),
            'country_id' => (int) ($data['country_id'] ?? 0),
            'city_id' => (int) ($data['city_id'] ?? 0),
            'street_address' => trim((string) ($data['street_address'] ?? '')),
        ];

        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
            $avatar = $this->validatedAvatar($_FILES['avatar'], $errors);

            if ($avatar !== null) {
                try {
                    $payload['avatar_url'] = (new GoogleCloudStorage())->uploadPublicObject(
                        $this->avatarObjectName($userId, $avatar['extension']),
                        $avatar['content'],
                        $avatar['mime_type']
                    );
                } catch (Throwable $exception) {
                    $errors[] = 'Could not upload avatar to Google Cloud Storage: ' . $exception->getMessage();
                }
            }
        }

        if ($errors !== []) {
            $this->flash('errors', $errors);
            $this->old($data);
            $this->redirect('/profile');
        }

        (new User())->updateProfile($userId, $payload);
        if ((new CV())->findByUserId($userId) !== null) {
            (new CV())->updateForUser($userId, $cvPayload);
        }

        $_SESSION['user']['full_name'] = $payload['full_name'];
        $_SESSION['user']['email'] = $payload['email'];
        if (isset($payload['avatar_url'])) {
            $_SESSION['user']['avatar_url'] = $payload['avatar_url'];
        }

        $this->flash('success', 'Your profile has been updated.');
        $this->redirect('/profile');
    }

    private function validateProfile(array $data, int $userId): array
    {
        $errors = [];
        $email = strtolower(trim((string) ($data['email'] ?? '')));

        if (trim((string) ($data['full_name'] ?? '')) === '') {
            $errors[] = 'Full name is required.';
        }

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        } elseif ((new User())->emailExistsForAnotherUser($email, $userId)) {
            $errors[] = 'This email is already used by another account.';
        }

        $countryId = (int) ($data['country_id'] ?? 0);
        $cityId = (int) ($data['city_id'] ?? 0);

        if ($countryId > 0 && ! (new Country())->exists($countryId)) {
            $errors[] = 'Selected country is invalid.';
        }

        if ($cityId > 0) {
            $city = (new City())->find($cityId);

            if ($city === null || ($countryId > 0 && (int) $city['country_id'] !== $countryId)) {
                $errors[] = 'Selected city is invalid for the selected country.';
            }
        }

        return $errors;
    }

    private function validatedAvatar(array $file, array &$errors): ?array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $errors[] = 'Avatar upload failed. Please choose another image.';
            return null;
        }

        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            $errors[] = 'Avatar image must be 2MB or smaller.';
            return null;
        }

        $tmpName = (string) ($file['tmp_name'] ?? '');
        $mimeType = '';

        try {
            $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($tmpName) ?: '';
        } catch (Throwable) {
            $mimeType = '';
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (! in_array($mimeType, $allowedTypes, true)) {
            $errors[] = 'Avatar must be a JPG, PNG, WEBP, or GIF image.';
            return null;
        }

        $content = file_get_contents($tmpName);
        if ($content === false) {
            $errors[] = 'Could not read the uploaded avatar image.';
            return null;
        }

        return [
            'content' => $content,
            'mime_type' => $mimeType,
            'extension' => $this->extensionForMimeType($mimeType),
        ];
    }

    private function extensionForMimeType(string $mimeType): string
    {
        return match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => 'bin',
        };
    }

    private function avatarObjectName(int $userId, string $extension): string
    {
        return 'avatars/user-' . $userId . '-' . time() . '.' . $extension;
    }

    private function profileHeadline(?array $cv, ?string $roleName): string
    {
        if ($roleName === 'employer') {
            return 'Active Employer';
        }

        if ($roleName === 'admin') {
            return 'Active Administrator';
        }

        $workHistories = $cv['work_histories'] ?? [];

        if (isset($workHistories[0]['job_title_name']) && trim((string) $workHistories[0]['job_title_name']) !== '') {
            return (string) $workHistories[0]['job_title_name'];
        }

        if (isset($cv['category_name']) && trim((string) $cv['category_name']) !== '') {
            return (string) $cv['category_name'] . ' Candidate';
        }

        return $roleName === 'job_seeker' ? 'Active Job Seeker' : 'Active User';
    }

    private function profileCompletion(?array $cv): int
    {
        if ($cv === null) {
            return 0;
        }

        $sections = [
            trim((string) ($cv['full_name'] ?? '')) !== ''
                && trim((string) ($cv['email'] ?? '')) !== ''
                && trim((string) ($cv['phone_number'] ?? '')) !== '',
            trim((string) ($cv['country_name'] ?? '')) !== ''
                && trim((string) ($cv['city_name'] ?? '')) !== ''
                && trim((string) ($cv['street_address'] ?? '')) !== '',
            ($cv['educations'] ?? []) !== [],
            ($cv['work_histories'] ?? []) !== [],
            ($cv['certificates'] ?? []) !== [],
            ($cv['skills'] ?? []) !== [],
        ];

        $completeSections = count(array_filter($sections));

        return (int) round(($completeSections / count($sections)) * 100);
    }
}
