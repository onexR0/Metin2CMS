<?php

declare(strict_types=1);
namespace oneX\Controllers;
use oneX\Core\Controller;
use oneX\Models\News;
use oneX\Models\Player;
use oneX\Models\Guild;
use oneX\Models\Setting;
use oneX\Models\GameStats;
use oneX\Models\SocialLink;
use oneX\Models\Page;

class HomeController extends Controller
{
	public function index(): void
	{
		$news       = News::latest(5);
		$topPlayers = Player::topPlayers(5);
		$topGuilds  = Guild::topGuilds(5);
		$account    = $this->currentAccount();

		$settings   = Setting::all();
		$stats      = GameStats::get();
		$social     = SocialLink::all();
		$footerPages= Page::all();

		$this->view('home', [
			'title'       => t('news'),
			'news'        => $news,
			'topPlayers'  => $topPlayers,
			'topGuilds'   => $topGuilds,
			'account'     => $account,
			'settings'    => $settings,
			'stats'       => $stats,
			'socialLinks' => $social,
			'footerPages' => $footerPages,
		]);
	}
}
