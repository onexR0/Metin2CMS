<?php

declare(strict_types=1);
namespace oneX\Controllers\Admin;
use oneX\Core\Controller;
use oneX\Models\News;
use oneX\Core\HtmlSanitizer;

class NewsController extends Controller
{
	public function index(): void
	{
		$this->requireAdmin();

		$news = News::all();

		$this->view('admin/news_list', [
			'title' => t('news'),
			'news'  => $news,
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
			
			News::create($title, $content);
		}

		$this->redirect('/admin/news');
	}

	public function edit(): void
	{
		$this->requireAdmin();

		$id = (int)($_GET['id'] ?? 0);
		if ($id <= 0) {
			$this->redirect('/admin/news');
		}

		$item = News::find($id);
		if (!$item) {
			$this->redirect('/admin/news');
		}

		$this->view('admin/news_edit', [
			'title'   => t('news'),
			'newsItem'=> $item,
		]);
	}

	public function update(): void
	{
		$this->requireAdmin();

		$id      = (int)($_POST['id'] ?? 0);
		$title   = trim($_POST['title'] ?? '');
		$content = trim($_POST['content'] ?? '');

		if ($id > 0 && $title !== '' && $content !== '') {
			$title = HtmlSanitizer::sanitizeText($title);
			$content = HtmlSanitizer::sanitize($content);
			
			News::update($id, $title, $content);
		}

		$this->redirect('/admin/news');
	}

	public function delete(): void
	{
		$this->requireAdmin();

		$id = (int)($_POST['id'] ?? 0);
		if ($id > 0) {
			News::delete($id);
		}

		$this->redirect('/admin/news');
	}
}
