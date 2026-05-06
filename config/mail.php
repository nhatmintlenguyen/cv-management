<?php

return [
    'host' => getenv('MAIL_HOST') ?: '127.0.0.1',
    'port' => (int) (getenv('MAIL_PORT') ?: 1025),
    'timeout' => (int) (getenv('MAIL_TIMEOUT') ?: 10),
    'username' => getenv('MAIL_USERNAME') ?: null,
    'password' => getenv('MAIL_PASSWORD') ?: null,
    'encryption' => getenv('MAIL_ENCRYPTION') ?: null,
    'from_email' => getenv('MAIL_FROM_EMAIL') ?: 'no-reply@onecv.local',
    'from_name' => getenv('MAIL_FROM_NAME') ?: 'OneCV',
];
