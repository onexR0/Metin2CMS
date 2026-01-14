<?php

declare(strict_types=1);
namespace oneX\Controllers\Admin;
use oneX\Core\Controller;
use oneX\Models\Setting;

class SettingsController extends Controller
{
	public function index(): void
	{
		$this->requireAdmin();

		$settings = Setting::all();

		$this->view('admin/settings', [
			'title'    => t('settings'),
			'settings' => $settings,
		]);
	}

	public function save(): void
	{
		$this->requireAdmin();

		$fields = [
			'show_players_online',
			'show_players_24h',
			'show_accounts_total',
			'show_characters_total',
			'show_guilds_total',
			'register_enabled',
		];

		foreach ($fields as $f) {
			$val = isset($_POST[$f]) ? '1' : '0';
			Setting::set($f, $val);
		}

		$this->redirect('/admin/settings');
	}
}
