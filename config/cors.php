<?php

return [

	/*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

	'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],

	'allowed_methods' => ['*'],

	'allowed_origins' => ['*'],

	'allowed_origins_patterns' => [],

	'allow_headers' => [
		'Content-Type',
		'X-Auth-Token',
		'Origin',
		'Authorization',
		'Set-Cookie'
	],

	'expose_headers' => [
		'Cache-Control',
		'Content-Language',
		'Content-Type',
		'Expires',
		'Last-Modified',
		'Pragma',
		'Set-Cookie'
	],

	'max_age' => 0,

	'supports_credentials' => true,

	'allow_credentials' => true,

];
