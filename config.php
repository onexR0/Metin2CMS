<?php

return [
	'app' => [
		'site_name'   => 'One-x.Ro',								//nume site
		'base_url'     => 'http://localhost',					//link http:// sau https://
		'theme'        => 'default',								//tema pui numele folderului din themes blue,green,orange,red sau iti poti crea propria tema bazata pe template
		'session_name' => 'Ejjpp920E9k0vHT',					//schimba cu un nume random pentru siguranta.
		'password_hash' => 'md5',								// 'md5' | 'sha1' (md5 pentru criptare parola servere normale sau sha1 pentru svfiles owsap)
	],

	'db' => [
		'host' => 'localhost',									//ip server
		'port' => 3306,
		'user' => 'root',										//user mysql
		'pass' => 'onex',										//parola mysql
		'charset' => 'utf8mb4',									//poti pune latin1 sau cum ai pe server sau poti lasa asa

		'databases' => [
			'account' => 'account',								// in caz ca ai alt nume la tabel gen srv1_account, speed_account etc...
			'player'  => 'player',								// in caz ca ai alt nume la tabel gen srv1_player, speed_player etc...  
		],
	],

	'lang' => [
		'default'   => 'ro',															//limba de baza ce se seteaza automat la prima intrare pe site.
		'available' => ['cz','de','en','es','fr','gr','hu','it','pl','pt','ro','tr'],	//lista de limbi in care este tradus tot site
	],

	'recaptcha' => [
		'enabled'    => true,											//poti activa sau dezactiva recaptcha pentru login/register/recuperare parola...
		'site_key'   => '6Ldy7kMsAAAAALJgnIzHBZbn8q1XDeX1OCiJBKEI',		//https://www.google.com/recaptcha/admin
		'secret_key' => '6Ldy7kMsAAAAAK3Fp22KFR98fn9E0JiQBsB7lMTc',		//https://www.google.com/recaptcha/admin
	],

	'mail' => [
		'from_name'  => 'Onex Server',									//Numele care apare cand primesti un mail.
		'smtp' => [
			'enabled'  => true,
			'host'     => 'mail.server.ro',								//server mail
			'port'     => 587,											//port server mail tls/ssl
			'username' => 'no-rely@server.ro',							//email
			'password' => 'password',									//parola
			'encryption' => 'tls',										//ssl sau tls
		],
	],
];
