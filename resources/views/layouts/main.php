<?php

use App\Core\View;

$app = require dirname(__DIR__, 3) . '/config/app.php';
$title = $title ?? $app['name'];
$siteName = $app['name'];
$defaultDescription = 'OneCV helps job seekers build structured online CVs and helps employers discover candidates and job vacancies through searchable, role-based tools.';
$metaDescription = $metaDescription ?? $defaultDescription;
$robots = $robots ?? 'index,follow';
$scheme = (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$absoluteUrl = static fn (string $path): string => $scheme . '://' . $host . $path;
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$canonicalUrl = $canonicalUrl ?? $absoluteUrl(isset($canonicalPath) ? View::url($canonicalPath) : $requestPath);
$pageTitle = $title . ' | ' . $siteName;
$ogType = $ogType ?? 'website';
$ogImage = $ogImage ?? $absoluteUrl(View::asset('images/onecv-og.svg'));
$structuredData = $structuredData ?? [
    [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $siteName,
        'url' => $absoluteUrl(View::url('/')),
    ],
    [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $siteName,
        'url' => $absoluteUrl(View::url('/')),
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => $absoluteUrl(View::url('/jobs')) . '?keyword={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ],
];
$errors = $_SESSION['_flash']['errors'] ?? [];
$success = $_SESSION['_flash']['success'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= View::e($pageTitle) ?></title>
    <meta name="description" content="<?= View::e($metaDescription) ?>">
    <meta name="robots" content="<?= View::e($robots) ?>">
    <link rel="canonical" href="<?= View::e($canonicalUrl) ?>">
    <meta property="og:site_name" content="<?= View::e($siteName) ?>">
    <meta property="og:type" content="<?= View::e($ogType) ?>">
    <meta property="og:title" content="<?= View::e($ogTitle ?? $pageTitle) ?>">
    <meta property="og:description" content="<?= View::e($ogDescription ?? $metaDescription) ?>">
    <meta property="og:url" content="<?= View::e($canonicalUrl) ?>">
    <meta property="og:image" content="<?= View::e($ogImage) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= View::e($ogTitle ?? $pageTitle) ?>">
    <meta name="twitter:description" content="<?= View::e($ogDescription ?? $metaDescription) ?>">
    <meta name="twitter:image" content="<?= View::e($ogImage) ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600;700&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap">
    <link rel="stylesheet" href="<?= View::asset('css/app.css') ?>">
    <script type="application/ld+json">
        <?= json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
    </script>
    <script>
        window.OneCV = {
            basePath: <?= json_encode(defined('APP_BASE_PATH') ? APP_BASE_PATH : '') ?>
        };
    </script>
    <script defer src="<?= View::asset('js/app.js') ?>"></script>
</head>
<body>
    <?php if ($errors !== [] || $success !== null): ?>
        <div class="flash-stack" aria-live="polite">
            <?php if ($success !== null): ?>
                <div class="flash flash-success"><?= View::e($success) ?></div>
            <?php endif; ?>

            <?php foreach ($errors as $error): ?>
                <div class="flash flash-error"><?= View::e($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?= $content ?>
    <?php require dirname(__DIR__) . '/partials/site-footer.php'; ?>
</body>
</html>
