<?php

declare(strict_types=1);
namespace oneX\Core;

class SecurityHeaders
{
	public static function set(): void
	{
		header('X-Frame-Options: SAMEORIGIN');
		header('X-XSS-Protection: 1; mode=block');
		header('X-Content-Type-Options: nosniff');
		header('Referrer-Policy: strict-origin-when-cross-origin');
		header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://www.google.com https://www.gstatic.com https://cdn.ckeditor.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.ckeditor.com; img-src 'self' data: https:; font-src 'self' data: https://fonts.gstatic.com; frame-src 'self' https://www.google.com https://www.youtube.com https://player.vimeo.com; connect-src 'self';");

		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
			header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
		}

		header('Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=(), usb=()');
		header('X-Permitted-Cross-Domain-Policies: none');
	}
}
