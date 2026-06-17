<?php

declare(strict_types=1);

return [
    'name' => $_ENV['APP_NAME'] ?? 'ERP Stocco',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN),
    'url' => $_ENV['APP_URL'] ?? 'http://localhost:8000',
    'jwt' => [
        'secret' => $_ENV['JWT_SECRET'] ?? 'default_secret',
        'expiry' => (int) ($_ENV['JWT_EXPIRY'] ?? 86400),
    ],
    'cors' => [
        'origin' => $_ENV['CORS_ORIGIN'] ?? 'http://localhost:5173',
    ],
];
