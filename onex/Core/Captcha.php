<?php

declare(strict_types=1);
namespace oneX\Core;

class Captcha
{
	public static function verify(?string $response, array $config): bool
	{
		if (empty($config['recaptcha']['enabled'])) {
			return true;
		}
		if (!$response) return false;

		$secret = $config['recaptcha']['secret_key'] ?? '';
		if ($secret === '') return false;

		$data = [
			'secret'   => $secret,
			'response' => $response,
			'remoteip' => $_SERVER['REMOTE_ADDR'] ?? null,
		];

		$opts = [
			'http' => [
				'method'  => 'POST',
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'content' => http_build_query($data),
				'timeout' => 5,
			],
		];

		$context = stream_context_create($opts);
		$result  = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);

		if ($result === false) return false;
		$json = json_decode($result, true);
		return !empty($json['success']);
	}
}
