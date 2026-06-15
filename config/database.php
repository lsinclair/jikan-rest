<?php

// When MONGODB_URL is set (e.g. DO Managed MongoDB full TLS URL), use it directly.
// Otherwise build the DSN from individual host/port/credentials for local dev.
$mongodb_url = env('MONGODB_URL');
if ($mongodb_url) {
    $dsn = $mongodb_url;
} else {
    $db_username = env('DB_USERNAME', env("APP_ENV") === "testing" ? "" : "admin");
    $dsn = "mongodb://";
    if (empty($db_username)) {
        $dsn .= env('DB_HOST', 'localhost').":".env('DB_PORT', 27017)."/".env('DB_ADMIN', 'admin');
    } else {
        $dsn .= env('DB_USERNAME', 'admin').":".env('DB_PASSWORD', '')."@"
              . env('DB_HOST', 'localhost').":".env('DB_PORT', 27017)."/".env('DB_ADMIN', 'admin');
    }
}

$redis_scheme = env('REDIS_SCHEME', 'tcp');

return [
    'default' => env('DB_CONNECTION', 'mongodb'),

    'connections' => [
        'mongodb' => [
            'driver'   => 'mongodb',
            'dsn'      => $dsn,
            'database' => env('DB_DATABASE', 'jikan'),
        ]
    ],

    'redis' => [
        'client' => 'predis',
        'default' => [
            'scheme'   => $redis_scheme,
            'host'     => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port'     => env('REDIS_PORT', 6379),
            'database' => 0,
            'ssl'      => $redis_scheme === 'tls' ? ['verify_peer' => false] : [],
        ]
    ],

    'migrations' => 'migrations'
];
