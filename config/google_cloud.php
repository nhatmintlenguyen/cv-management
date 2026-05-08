<?php

return [
    'service_account_path' => getenv('GOOGLE_CLOUD_SERVICE_ACCOUNT_PATH')
        ?: dirname(__DIR__) . '/project-2aa0e6cc-d48c-40e3-884-28e6fb59d79e.json',
    'storage_bucket' => getenv('GOOGLE_CLOUD_STORAGE_BUCKET') ?: 'cv-management',
    'public_base_url' => getenv('GOOGLE_CLOUD_PUBLIC_BASE_URL') ?: 'https://storage.googleapis.com',
    'use_public_read_acl' => false,
    'return_signed_url' => true,
    'signed_url_ttl' => 60 * 60 * 24 * 365 * 10,
];
