<?php

declare(strict_types=1);
namespace oneX\Core;
require_once __DIR__ . '/../../vendor/phpmailer/Exception.php';
require_once __DIR__ . '/../../vendor/phpmailer/PHPMailer.php';
require_once __DIR__ . '/../../vendor/phpmailer/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
	public static function send(array $config, string $to, string $subject, string $htmlBody): bool
	{
		if (isset($config['mail']['smtp']['enabled']) && $config['mail']['smtp']['enabled']) {
			return self::sendViaSMTP($config, $to, $subject, $htmlBody);
		}
		
		return self::sendViaPhpMail($config, $to, $subject, $htmlBody);
	}

	private static function sendViaSMTP(array $config, string $to, string $subject, string $htmlBody): bool
	{
		try {
			$mail = new PHPMailer(true);
			$mail->isSMTP();
			$mail->Host       = $config['mail']['smtp']['host'];
			$mail->SMTPAuth   = true;
			$mail->Username   = $config['mail']['smtp']['username'];
			$mail->Password   = $config['mail']['smtp']['password'];
			$mail->SMTPSecure = $config['mail']['smtp']['encryption'] ?? 'tls';
			$mail->Port       = $config['mail']['smtp']['port'];
			$mail->CharSet    = 'UTF-8';
			$mail->SMTPDebug = 0;
			$fromEmail = $config['mail']['smtp']['username'] ?? 'no-reply@one-x.ro';
			$fromName  = $config['mail']['from_name'] ?? 'oneX core';
			$mail->setFrom($fromEmail, $fromName);
			$mail->addAddress($to);
			$mail->isHTML(true);
			$mail->Subject = $subject;
			$mail->Body    = $htmlBody;
			$mail->AltBody = strip_tags($htmlBody);
			return $mail->send();
		} catch (Exception $e) {
			error_log("PHPMailer Error: {$mail->ErrorInfo}");
			return self::sendViaPhpMail($config, $to, $subject, $htmlBody);
		}
	}

	private static function sendViaPhpMail(array $config, string $to, string $subject, string $htmlBody): bool
	{
		$fromEmail = $config['mail']['from_email'] ?? 'no-reply@one-x.ro';
		$fromName  = $config['mail']['from_name'] ?? 'oneX core';
		$headers = [];
		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-type: text/html; charset=utf-8';
		$headers[] = 'From: ' . sprintf('"%s" <%s>', $fromName, $fromEmail);
		$headersStr = implode("\r\n", $headers);
		return mail(
			$to,
			'=?UTF-8?B?' . base64_encode($subject) . '?=',
			$htmlBody,
			$headersStr
		);
	}

	public static function sendVerificationCode(
		array $config,
		string $to,
		string $code,
		string $type = 'password_reset'
	): bool {
		$siteName = $config['app']['site_name'] ?? 'oneX core';
		
		if ($type === 'password_reset') {
			$subject = "{$siteName} - " . t('mail_template.code_rest_pw');
			$htmlBody = self::getPasswordResetEmailTemplate($siteName, $code);
		} else {
			$subject = "{$siteName} - " . t('mail_template.code_confirm_mail');
			$htmlBody = self::getEmailChangeTemplate($siteName, $code);
		}

		return self::send($config, $to, $subject, $htmlBody);
	}

	public static function sendSocialId(
		array $config,
		string $to,
		string $socialId,
		string $username
	): bool {
		$siteName = $config['app']['site_name'] ?? 'oneX core';
		$subject = "{$siteName} - " . t('mail_template.delete_character');
		$htmlBody = self::getSocialIdEmailTemplate($siteName, $socialId, $username);

		return self::send($config, $to, $subject, $htmlBody);
	}

	public static function sendWarehousePassword(
		array $config,
		string $to,
		string $password,
		string $username
	): bool {
		$siteName = $config['app']['site_name'] ?? 'oneX core';
		$subject = "{$siteName} - " . t('mail_template.warehouse_password');
		$htmlBody = self::getWarehousePasswordEmailTemplate($siteName, $password, $username);

		return self::send($config, $to, $subject, $htmlBody);
	}

	private static function getPasswordResetEmailTemplate(string $siteName, string $code): string
	{
		return "
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset='UTF-8'>
			<title>{$siteName} - " . t('mail_template.reset_password') . "</title>
			<style>
				body {
					margin: 0;
					padding: 0;
					background-color: #f3f4f6;
					font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
					color: #111827;
				}
				.container {
					max-width: 600px;
					margin: 24px auto;
					background-color: #ffffff;
					border: 1px solid #e5e7eb;
					border-radius: 8px;
					padding: 24px 32px;
				}
				.header {
					text-align: center;
					margin-bottom: 24px;
				}
				.header h1 {
					margin: 0;
					font-size: 22px;
					color: #111827;
				}
				.content {
					font-size: 14px;
					line-height: 1.6;
				}
				.code-box {
					margin: 16px auto;
					padding: 8px 12px;
					border-radius: 4px;
					background-color: #eff6ff;
					border: 1px solid #2563eb;
					text-align: center;
					max-width: 260px;
				}
				
				.code {
					font-size: 18px;
					font-weight: 700;
					color: #1d4ed8;
					letter-spacing: 2px;
				}
				.footer {
					margin-top: 28px;
					padding-top: 16px;
					border-top: 1px solid #e5e7eb;
					text-align: center;
					font-size: 12px;
					color: #6b7280;
				}
			</style>
		</head>
		<body>
			<div class='container'>
				<div class='header'>
					<h1>{$siteName}</h1>
				</div>
				<div class='content'>
					<p>" . t('mail_template.mail_hello') . "</p>
					<p>" . t('mail_template.password_request_note') . "</p>
					
					<div class='code-box'>
						<div class='code'>{$code}</div>
					</div>
					

				</div>
				<div class='footer'>
					<p>© " . date('Y') . " {$siteName}. " . t('mail_template.mail_copyright') . "</p>
				</div>
			</div>
		</body>
		</html>
		";
	}

	private static function getEmailChangeTemplate(string $siteName, string $code): string
	{
		return "
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset='UTF-8'>
			<title>{$siteName} - " . t('mail_template.change_mail') . "</title>
			<style>
				body {
					margin: 0;
					padding: 0;
					background-color: #f3f4f6;
					font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
					color: #111827;
				}
				.container {
					max-width: 600px;
					margin: 24px auto;
					background-color: #ffffff;
					border: 1px solid #e5e7eb;
					border-radius: 8px;
					padding: 24px 32px;
				}
				.header {
					text-align: center;
					margin-bottom: 24px;
				}
				.header h1 {
					margin: 0;
					font-size: 22px;
					color: #111827;
				}
				.content {
					font-size: 14px;
					line-height: 1.6;
				}
				.code-box {
					margin: 16px auto;
					padding: 8px 12px;
					border-radius: 4px;
					background-color: #eff6ff;
					border: 1px solid #2563eb;
					text-align: center;
					max-width: 260px;
				}
				
				.code {
					font-size: 18px;
					font-weight: 700;
					color: #1d4ed8;
					letter-spacing: 2px;
				}
				.footer {
					margin-top: 28px;
					padding-top: 16px;
					border-top: 1px solid #e5e7eb;
					text-align: center;
					font-size: 12px;
					color: #6b7280;
				}
			</style>
		</head>
		<body>
			<div class='container'>
				<div class='header'>
					<h1>{$siteName}</h1>
				</div>
				<div class='content'>
					<p>" . t('mail_template.mail_hello') . "</p>
					<p>" . t('mail_template.change_mail_note') . "</p>
					
					<div class='code-box'>
						<div class='code'>{$code}</div>
					</div>
					

				</div>
				<div class='footer'>
					<p>© " . date('Y') . " {$siteName}. " . t('mail_template.mail_copyright') . "</p>
				</div>
			</div>
		</body>
		</html>
		";
	}

	private static function getSocialIdEmailTemplate(string $siteName, string $socialId, string $username): string
	{
		return "
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset='UTF-8'>
			<title>{$siteName} - " . t('mail_template.delete_character') . "</title>
			<style>
				body {
					margin: 0;
					padding: 0;
					background-color: #f3f4f6;
					font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
					color: #111827;
				}
				.container {
					max-width: 600px;
					margin: 24px auto;
					background-color: #ffffff;
					border: 1px solid #e5e7eb;
					border-radius: 8px;
					padding: 24px 32px;
				}
				.header {
					text-align: center;
					margin-bottom: 24px;
				}
				.header h1 {
					margin: 0;
					font-size: 22px;
					color: #111827;
				}
				.content {
					font-size: 14px;
					line-height: 1.6;
				}
				.code-box {
					margin: 16px auto;
					padding: 8px 12px;
					border-radius: 4px;
					background-color: #eff6ff;
					border: 1px solid #2563eb;
					text-align: center;
					max-width: 260px;
				}
				
				.code {
					font-size: 18px;
					font-weight: 700;
					color: #1d4ed8;
					letter-spacing: 2px;
				}
				.footer {
					margin-top: 28px;
					padding-top: 16px;
					border-top: 1px solid #e5e7eb;
					text-align: center;
					font-size: 12px;
					color: #6b7280;
				}
			</style>
		</head>
		<body>
			<div class='container'>
				<div class='header'>
					<h1>{$siteName}</h1>
				</div>
				<div class='content'>
					<p>" . t('mail_template.mail_hello') . " <strong>{$username}</strong></p>
					<p>" . t('mail_template.detele_character_note') . "</p>
					
					<div class='code-box'>
						<div class='code'>{$socialId}</div>
					</div>
				</div>
				<div class='footer'>
					<p>© " . date('Y') . " {$siteName}. " . t('mail_template.mail_copyright') . "</p>
				</div>
			</div>
		</body>
		</html>
		";
	}

	private static function getWarehousePasswordEmailTemplate(string $siteName, string $password, string $username): string
	{
		return "
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset='UTF-8'>
			<title>{$siteName} - " . t('mail_template.warehouse_password') . "</title>
			<style>
				body {
					margin: 0;
					padding: 0;
					background-color: #f3f4f6;
					font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
					color: #111827;
				}
				.container {
					max-width: 600px;
					margin: 24px auto;
					background-color: #ffffff;
					border: 1px solid #e5e7eb;
					border-radius: 8px;
					padding: 24px 32px;
				}
				.header {
					text-align: center;
					margin-bottom: 24px;
				}
				.header h1 {
					margin: 0;
					font-size: 22px;
					color: #111827;
				}
				.content {
					font-size: 14px;
					line-height: 1.6;
				}
				.code-box {
					margin: 16px auto;
					padding: 8px 12px;
					border-radius: 4px;
					background-color: #eff6ff;
					border: 1px solid #2563eb;
					text-align: center;
					max-width: 260px;
				}
				
				.code {
					font-size: 18px;
					font-weight: 700;
					color: #1d4ed8;
					letter-spacing: 2px;
				}
				.footer {
					margin-top: 28px;
					padding-top: 16px;
					border-top: 1px solid #e5e7eb;
					text-align: center;
					font-size: 12px;
					color: #6b7280;
				}
			</style>
		</head>
		<body>
			<div class='container'>
				<div class='header'>
					<h1>{$siteName}</h1>
				</div>
				<div class='content'>
					<p>" . t('mail_template.mail_hello') . " <strong>{$username}</strong></p>
					<p>" . t('mail_template.warehouse_password_note') . "</p>
					
					<div class='code-box'>
						<div class='code'>{$password}</div>
					</div>
					
				</div>
				<div class='footer'>
					<p>© " . date('Y') . " {$siteName}. " . t('mail_template.mail_copyright') . "</p>
				</div>
			</div>
		</body>
		</html>
		";
	}
}
