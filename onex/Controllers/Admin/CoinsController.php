<?php

declare(strict_types=1);
namespace oneX\Controllers\Admin;
use oneX\Core\Controller;
use oneX\Models\GameAccount;

class CoinsController extends Controller
{
	public function index(): void
	{
		$this->requireAdmin();

		$search = trim($_GET['search'] ?? '');
		$success = $_GET['success'] ?? '';
		$error = $_GET['error'] ?? '';
		
		$account = null;
		if ($search !== '') {
			try {
				$accounts = GameAccount::search($search);
				$account = !empty($accounts) ? $accounts[0] : null;
			} catch (\Throwable $e) {
				error_log("Admin Coins Search Error: " . $e->getMessage());
				error_log("Stack trace: " . $e->getTraceAsString());
				$error = t('error_searching_accounts');
			}
		}

		$this->view('admin/coins', [
			'title' => t('coins'),
			'account' => $account,
			'search' => $search,
			'success' => $success,
			'error' => $error,
		]);
	}

	public function update(): void
	{
		$this->requireAdmin();

		if (!$this->verifyCSRF()) {
			$this->redirect('/admin/coins?error=' . urlencode(t('csrf_token_invalid')));
			return;
		}

		$login = trim($_POST['login'] ?? '');
		$coins = trim($_POST['coins'] ?? '0');
		$jcoins = trim($_POST['jcoins'] ?? '0');
		
		if ($login !== '') {
			$coinsValue = (int)$coins;
			$jcoinsValue = (int)$jcoins;
			
			try {
				GameAccount::updateCoins($login, $coinsValue, $jcoinsValue);
				$this->redirect('/admin/coins?search=' . urlencode($login) . '&success=' . urlencode(t('coins_updated_success')));
			} catch (\Throwable $e) {
				error_log("Admin Update Coins Error: " . $e->getMessage());
				$this->redirect('/admin/coins?search=' . urlencode($login) . '&error=' . urlencode(t('error_updating_coins')));
			}
		} else {
			$this->redirect('/admin/coins');
		}
	}
}
