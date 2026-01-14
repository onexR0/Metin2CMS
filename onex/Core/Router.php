<?php

declare(strict_types=1);
namespace oneX\Core;

class Router
{
	private array $routes = [];

	public function get(string $path, array $action): void
	{
		$this->addRoute('GET', $path, $action);
	}

	public function post(string $path, array $action): void
	{
		$this->addRoute('POST', $path, $action);
	}

	private function addRoute(string $method, string $path, array $action): void
	{
		$path = '/' . ltrim($path, '/');
		$path = rtrim($path, '/') ?: '/';

		$this->routes[] = [
			'method' => $method,
			'path'   => $path,
			'action' => $action,
		];
	}

	public function dispatch(string $uri, string $method): void
	{
		$uriPath = parse_url($uri, PHP_URL_PATH) ?: '/';
		$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

		if ($scriptDir !== '/' && str_starts_with($uriPath, $scriptDir)) {
			$uriPath = substr($uriPath, strlen($scriptDir));
		}

		if ($uriPath === '' || $uriPath === false) {
			$uriPath = '/';
		}

		$uriPath = rtrim($uriPath, '/') ?: '/';

		foreach ($this->routes as $route) {
			if ($route['method'] === $method && $route['path'] === $uriPath) {
				[$class, $action] = $route['action'];
				$controller = new $class();
				$controller->$action();
				return;
			}
		}

		http_response_code(404);
		echo '404 Not Found';
	}
}
