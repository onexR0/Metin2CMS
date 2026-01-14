<?php

declare(strict_types=1);
namespace oneX\Controllers\Admin;
use oneX\Core\Controller;
use oneX\Models\DownloadLink;

class DownloadsController extends Controller
{
	public function index(): void
	{
		$this->requireAdmin();

		$links = DownloadLink::all();

		$this->view('admin/downloads', [
			'title' => t('download'),
			'links' => $links,
		]);
	}

	public function create(): void
	{
		$this->requireAdmin();

		$label = trim($_POST['label'] ?? '');
		$url   = trim($_POST['url'] ?? '');

		if ($label !== '' && $url !== '') {
			DownloadLink::create($label, $url);
		}

		$this->redirect('/admin/downloads');
	}

	public function delete(): void
	{
		$this->requireAdmin();

		$id = (int)($_POST['id'] ?? 0);
		if ($id > 0) {
			DownloadLink::delete($id);
		}

		$this->redirect('/admin/downloads');
	}
}
