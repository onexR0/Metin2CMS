<?php

declare(strict_types=1);
namespace oneX\Controllers;
use oneX\Core\Controller;
use oneX\Models\Page;

class PageController extends Controller
{
	public function show(): void
	{
		$slug = $_GET['slug'] ?? '';
		if ($slug === '') {
			$this->redirect('/');
		}

		$page = Page::findBySlug($slug);
		if (!$page) {
			http_response_code(404);
			echo 'Page not found';
			return;
		}

		$this->view('page', [
			'title' => $page['title'],
			'page'  => $page,
		]);
	}
}
