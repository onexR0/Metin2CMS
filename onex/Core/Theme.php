<?php

declare(strict_types=1);
namespace oneX\Core;

class Theme
{
	private string $name;
	private string $basePath;
	private string $assetsUrl;

	public function __construct(string $name, string $baseUrl)
	{
		$this->name      = $name;
		$this->basePath  = dirname(__DIR__, 2) . '/themes/' . $name . '/';
		$this->assetsUrl = rtrim($baseUrl, '/') . '/themes/' . $name . '/assets';

		if (!is_dir($this->basePath)) {
			throw new \RuntimeException("Theme '{$name}' not found.");
		}
	}

	public function getLayoutPath(): string
	{
		$layout = $this->basePath . 'layout.php';
		if (!file_exists($layout)) {
			throw new \RuntimeException("layout.php missing in theme '{$this->name}'.");
		}
		return $layout;
	}

	public function getPagePath(string $view): string
	{
		$file = $this->basePath . 'pages/' . $view . '.php';
		if (!file_exists($file)) {
			throw new \RuntimeException("Page '{$view}' missing in theme '{$this->name}'.");
		}
		return $file;
	}

	public function assetsUrl(): string
	{
		return $this->assetsUrl;
	}
}
