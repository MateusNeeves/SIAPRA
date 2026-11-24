<?php

return [
    'minutes' => (int) env('RECUPERACAO_MINUTES', 30),
    'login_limit' => (int) env('RECUPERACAO_LOGIN_LIMIT', 3),
    'ip_limit' => (int) env('RECUPERACAO_IP_LIMIT', 10),
    'floor_ms' => (int) env('RECUPERACAO_FLOOR_MS', 2500),
    'timezone' => env('RECUPERACAO_TIMEZONE', 'America/Bahia'),
    'blocked_emails' => ['admin@admin.com'],
];
