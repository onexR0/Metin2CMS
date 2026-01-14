<?php

declare(strict_types=1);
namespace oneX\Controllers;
use oneX\Core\Controller;
use oneX\Models\Player;
use oneX\Models\Guild;

class RankingController extends Controller
{
	public function players(): void
	{
		$page = max(1, (int)($_GET['page'] ?? 1));
		$perPage = 20;
		$total   = Player::totalForRanking();
		$maxPage = max(1, (int)ceil($total / $perPage));
		if ($page > $maxPage) $page = $maxPage;

		$players = Player::topPlayersPage($page, $perPage);

		$this->view('ranking_players', [
			'title'   => t('top_players'),
			'players' => $players,
			'page'    => $page,
			'maxPage' => $maxPage,
			'total'   => $total,
		]);
	}

	public function guilds(): void
	{
		$page = max(1, (int)($_GET['page'] ?? 1));
		$perPage = 20;
		$total   = Guild::totalForRanking();
		$maxPage = max(1, (int)ceil($total / $perPage));
		if ($page > $maxPage) $page = $maxPage;

		$guilds = Guild::topGuildsPage($page, $perPage);

		$this->view('ranking_guilds', [
			'title'  => t('top_guilds'),
			'guilds' => $guilds,
			'page'   => $page,
			'maxPage'=> $maxPage,
			'total'  => $total,
		]);
	}
}
