<?php

declare(strict_types=1);
namespace oneX\Controllers\Admin;
use oneX\Core\Controller;
use oneX\Models\GameStats;

class DashboardController extends Controller
{
	public function index(): void
	{
		$this->requireAdmin();

		$stats = GameStats::get();

		$this->view('admin/dashboard', [
			'title' => t('AdminDashboard'),
			'stats' => $stats,
		]);
	}
}
