<?php

declare(strict_types=1);
namespace oneX\Controllers\Admin;
use oneX\Core\Controller;
use oneX\Models\Page;
use oneX\Core\HtmlSanitizer;

class PagesController extends Controller
{
	public function index(): void
	{
		$this->requireAdmin();

		$pages = Page::all();

		$this->view('admin/pages', [
			'title' => t('pages'),
			'pages' => $pages,
		]);
	}

	public function create(): void
	{
		$this->requireAdmin();

		$title   = trim($_POST['title'] ?? '');
		$content = trim($_POST['content'] ?? '');

		if ($title !== '' && $content !== '') {
			$title = HtmlSanitizer::sanitizeText($title);
			$content = HtmlSanitizer::sanitize($content);
			
			Page::create($title, $content);
		}

		$this->redirect('/admin/pages');
	}

	public function edit(): void
	{
		$this->requireAdmin();

		$id = (int)($_GET['id'] ?? 0);
		$page = Page::findById($id);

		if (!$page) {
			$this->redirect('/admin/pages');
			return;
		}

		$this->view('admin/pages-edit', [
			'title' => t('pages'),
			'page' => $page,
		]);
	}

	public function update(): void
	{
		$this->requireAdmin();

		$id = (int)($_POST['id'] ?? 0);
		$title = trim($_POST['title'] ?? '');
		$content = trim($_POST['content'] ?? '');

		if ($id > 0 && $title !== '' && $content !== '') {
			$title = HtmlSanitizer::sanitizeText($title);
			$content = HtmlSanitizer::sanitize($content);
			
			Page::update($id, $title, $content);
		}

		$this->redirect('/admin/pages');
	}

	public function delete(): void
	{
		$this->requireAdmin();

		$id = (int)($_POST['id'] ?? 0);
		if ($id > 0) {
			Page::delete($id);
		}

		$this->redirect('/admin/pages');
	}
}
