<?php

declare(strict_types=1);
namespace oneX\Controllers\Admin;
use oneX\Core\Controller;
use oneX\Models\SocialLink;

class SocialController extends Controller
{
	public function index(): void
	{
		$this->requireAdmin();

		$links = SocialLink::all();

		$this->view('admin/social', [
			'title' => t('links'),
			'links' => $links,
		]);
	}

	public function save(): void
	{
		$this->requireAdmin();

		$links = [
			'discord'   => trim($_POST['discord'] ?? ''),
			'tiktok'    => trim($_POST['tiktok'] ?? ''),
			'ishop'     => trim($_POST['ishop'] ?? ''),
		];

		SocialLink::setAll($links);

		$this->redirect('/admin/social');
	}
}
