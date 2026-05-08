<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

class GoogleCloudStorage
{
    private array $config;
    private array $serviceAccount;

    public function __construct()
    {
        $this->config = require dirname(__DIR__, 2) . '/config/google_cloud.php';
        $this->serviceAccount = $this->loadServiceAccount((string) $this->config['service_account_path']);
    }

    public function uploadPublicObject(string $objectName, string $content, string $mimeType): string
    {
        $bucket = (string) $this->config['storage_bucket'];
        $token = $this->accessToken();
        $query = http_build_query([
            'uploadType' => 'media',
            'name' => $objectName,
        ]);

        if (! empty($this->config['use_public_read_acl'])) {
            $query .= '&predefinedAcl=publicRead';
        }

        $url = "https://storage.googleapis.com/upload/storage/v1/b/{$bucket}/o?{$query}";
        $this->request('POST', $url, [
            'Authorization: Bearer ' . $token,
            'Content-Type: ' . $mimeType,
            'Content-Length: ' . strlen($content),
        ], $content);

        if (! empty($this->config['return_signed_url'])) {
            return $this->signedObjectUrl($bucket, $objectName);
        }

        return $this->publicObjectUrl($bucket, $objectName);
    }

    private function loadServiceAccount(string $path): array
    {
        if (! is_file($path)) {
            throw new RuntimeException('Google service account key file was not found.');
        }

        $json = json_decode((string) file_get_contents($path), true);

        if (! is_array($json) || empty($json['client_email']) || empty($json['private_key']) || empty($json['token_uri'])) {
            throw new RuntimeException('Google service account key file is invalid.');
        }

        return $json;
    }

    private function accessToken(): string
    {
        $now = time();
        $assertion = $this->jwt([
            'iss' => $this->serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/devstorage.read_write',
            'aud' => $this->serviceAccount['token_uri'],
            'iat' => $now,
            'exp' => $now + 3600,
        ]);

        $response = $this->request('POST', (string) $this->serviceAccount['token_uri'], [
            'Content-Type: application/x-www-form-urlencoded',
        ], http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $assertion,
        ]));

        if (empty($response['access_token'])) {
            throw new RuntimeException('Google access token response did not include an access token.');
        }

        return (string) $response['access_token'];
    }

    private function jwt(array $payload): string
    {
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];

        if (! empty($this->serviceAccount['private_key_id'])) {
            $header['kid'] = $this->serviceAccount['private_key_id'];
        }

        $segments = [
            $this->base64UrlEncode(json_encode($header, JSON_THROW_ON_ERROR)),
            $this->base64UrlEncode(json_encode($payload, JSON_THROW_ON_ERROR)),
        ];
        $signingInput = implode('.', $segments);

        $signature = '';
        $signed = openssl_sign($signingInput, $signature, (string) $this->serviceAccount['private_key'], OPENSSL_ALGO_SHA256);

        if (! $signed) {
            throw new RuntimeException('Could not sign Google service account JWT.');
        }

        $segments[] = $this->base64UrlEncode($signature);

        return implode('.', $segments);
    }

    private function request(string $method, string $url, array $headers, string $body): array
    {
        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", $headers),
                'content' => $body,
                'ignore_errors' => true,
                'timeout' => 30,
            ],
        ]);

        $result = file_get_contents($url, false, $context);
        $statusLine = $http_response_header[0] ?? '';

        if ($result === false || ! preg_match('/\s2\d\d\s/', $statusLine)) {
            throw new RuntimeException('Google Cloud Storage request failed: ' . trim((string) $result));
        }

        if ($result === '') {
            return [];
        }

        $decoded = json_decode($result, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function publicObjectUrl(string $bucket, string $objectName): string
    {
        return rtrim((string) $this->config['public_base_url'], '/') . '/' . rawurlencode($bucket) . '/' . str_replace('%2F', '/', rawurlencode($objectName));
    }

    private function signedObjectUrl(string $bucket, string $objectName): string
    {
        $expires = time() + (int) ($this->config['signed_url_ttl'] ?? 3600);
        $resource = '/' . $bucket . '/' . $objectName;
        $stringToSign = implode("\n", [
            'GET',
            '',
            '',
            (string) $expires,
            $resource,
        ]);

        $signature = '';
        $signed = openssl_sign($stringToSign, $signature, (string) $this->serviceAccount['private_key'], OPENSSL_ALGO_SHA256);

        if (! $signed) {
            throw new RuntimeException('Could not sign Google Cloud Storage read URL.');
        }

        return $this->publicObjectUrl($bucket, $objectName) . '?' . http_build_query([
            'GoogleAccessId' => $this->serviceAccount['client_email'],
            'Expires' => $expires,
            'Signature' => base64_encode($signature),
        ]);
    }
}
