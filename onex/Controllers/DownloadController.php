<?php

declare(strict_types=1);
namespace oneX\Controllers;
use oneX\Core\Controller;
use oneX\Models\DownloadLink;

class DownloadController extends Controller
{
	public function index(): void
	{
		$links = DownloadLink::all();

		$this->view('download', [
			'title' => t('download'),
			'links' => $links,
		]);
	}
}
