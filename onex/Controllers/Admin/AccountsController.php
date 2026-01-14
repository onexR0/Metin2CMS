<?php
declare(strict_types=1);
namespace oneX\Controllers\Admin;
use oneX\Core\Controller;
use oneX\Models\GameAccount;

class AccountsController extends Controller
{
	public function index(): void
	{
		$this->requireAdmin();

		$search = trim($_GET['search'] ?? '');
		
		try {
			$accounts = $search !== '' ? GameAccount::search($search) : [];
		} catch (\Throwable $e) {
			error_log("Admin Account Search Error: " . $e->getMessage());
			error_log("Stack trace: " . $e->getTraceAsString());
			$accounts = [];
			$_SESSION['admin_error'] = t('error_searching_accounts');
		}

		$this->view('admin/accounts', [
			'title' => t('accounts'),
			'accounts' => $accounts,
			'search' => $search,
			'success' => $_GET['success'] ?? null,
			'error' => $_GET['error'] ?? $_SESSION['admin_error'] ?? null,
		]);
		
		unset($_SESSION['admin_error']);
	}

	public function ban(): void
	{
		$this->requireAdmin();

		$login = trim($_POST['login'] ?? '');
		$search = trim($_POST['search'] ?? '');
		
		if ($login !== '') {
			GameAccount::updateStatus($login, 'BLOCK');
			$message = 'success=' . urlencode(t('account_banned_success'));
		} else {
			$message = 'error=' . urlencode('Eroare: The account could not be banned.');
		}

		$redirectUrl = '/admin/accounts?' . $message;
		if ($search !== '') {
			$redirectUrl .= '&search=' . urlencode($search);
		}
		
		$this->redirect($redirectUrl);
	}

	public function unban(): void
	{
		$this->requireAdmin();

		$login = trim($_POST['login'] ?? '');
		$search = trim($_POST['search'] ?? '');
		
		if ($login !== '') {
			GameAccount::updateStatus($login, 'OK');
			$message = 'success=' . urlencode(t('account_unbanned_success'));
		} else {
			$message = 'error=' . urlencode('Error: Unable to unban the account.');
		}

		$redirectUrl = '/admin/accounts?' . $message;
		if ($search !== '') {
			$redirectUrl .= '&search=' . urlencode($search);
		}
		
		$this->redirect($redirectUrl);
	}
}
