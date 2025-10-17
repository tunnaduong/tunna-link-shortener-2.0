<?php

return [
  'app_name' => 'Tunna Duong Link Shortener',
  'app_url' => $_ENV['APP_URL'] ?? 'https://tunn.ad',
  'app_env' => $_ENV['APP_ENV'] ?? 'production',

  'database' => [
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'username' => $_ENV['DB_USERNAME'] ?? 'links',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'database' => $_ENV['DB_NAME'] ?? 'links',
    'charset' => 'utf8'
  ],

  'recaptcha' => [
    'secret_key' => $_ENV['RECAPTCHA_SECRET_KEY'] ?? '6Ldga7MqAAAAANQwYsiNr6DJw70CvNqpsZPjLthL',
    'site_key' => $_ENV['RECAPTCHA_SITE_KEY'] ?? '6Ldga7MqAAAAAMaec8Hyk87vZksRcLUusHvYokX0'
  ],

  'adsense' => [
    'client' => $_ENV['GOOGLE_ADSENSE_CLIENT'] ?? 'ca-pub-3425905751761094'
  ]
];
