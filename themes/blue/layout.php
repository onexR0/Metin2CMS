<?php
use oneX\Models\Setting;
$registerEnabled     = Setting::get('register_enabled', '1') === '1';
$showPlayersOnline   = Setting::get('show_players_online', '1') === '1';
$showPlayers24h      = Setting::get('show_players_24h', '1') === '1';
$showAccountsTotal   = Setting::get('show_accounts_total', '1') === '1';
$showCharactersTotal = Setting::get('show_characters_total', '1') === '1';
$showGuildsTotal     = Setting::get('show_guilds_total', '1') === '1';
$isHomePage = isset($isHomePage) && $isHomePage === true;
$loggedIn = !empty($_SESSION['account_id']);
$hasAnyStatEnabled = $showPlayersOnline || $showPlayers24h || $showAccountsTotal || $showCharactersTotal || $showGuildsTotal;
?>


<!doctype html>
<html lang="ro">
<head>
	<meta charset="utf-8">
	<title><?= $siteName . " - " . $title ?></title>
	<link rel="icon" type="image/x-icon" href="<?= $assetsUrl; ?>/img/favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="<?= $assetsUrl; ?>/css/style.css?v=15.5">
	<link rel="stylesheet" href="<?= $assetsUrl; ?>/css/default.css?v=4.4">
	<link rel="stylesheet" href="<?= $assetsUrl; ?>/css/mobile-responsive.css?v=4.2">
</head>
<body>

<header class="site-header">
	<div class="header-inner container">
		<button class="mobile-menu-toggle" type="button" aria-label="Toggle menu">
			<span class="hamburger-line"></span>
			<span class="hamburger-line"></span>
			<span class="hamburger-line"></span>
		</button>

		<div class="top-bar">
			<nav class="top-nav">
				<a href="<?= $baseUrl; ?>"><?= t('home'); ?></a>

				<?php if ($registerEnabled): ?>
					<a href="<?= $baseUrl; ?>/register"><?= t('register'); ?></a>
				<?php endif; ?>

				<div class="dropdown dropdown--nav">
					<button class="dropdown__toggle" type="button"><?= t('ranking'); ?></button>
					<div class="dropdown__menu">
						<div class="dropdown__menu-inner">
							<a href="<?= $baseUrl; ?>/ranking-players"><?= t('players'); ?></a>
							<a href="<?= $baseUrl; ?>/ranking-guilds"><?= t('guilds'); ?></a>
						</div>
					</div>
				</div>
				<a href="<?= $baseUrl; ?>/download"><?= t('download'); ?></a>
				<?php if (!empty($socialLinks['discord'])): ?>
					<a href="<?= $socialLinks['discord']; ?>">Discord</a>
				<?php endif; ?>

				<?php if (!empty($socialLinks['tiktok'])): ?>
					<a href="<?= $socialLinks['tiktok']; ?>">TikTok</a>
				<?php endif; ?>
			</nav>
		</div>

		<div class="logo-section">
			<div class="logo-center">
				<a href="<?= $baseUrl; ?>">
					<img src="<?= $assetsUrl; ?>/img/logo.png" alt="<?= $siteName; ?>" class="logo-image">
				</a>
			</div>

			<div class="header-right-below">
				<div class="dropdown dropdown--lang">
					<button class="dropdown__toggle" type="button">
						<img src="<?= $assetsUrl; ?>/flags/<?= strtolower($currentLang); ?>.png" alt="<?= strtoupper($currentLang); ?>" class="flag-icon">
					</button>

					<div class="dropdown__menu">
						<div class="dropdown__menu-inner">
							<?php foreach ($languages as $code): ?>
								<a href="?lang=<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?>"> <img src="<?= $assetsUrl; ?>/flags/<?= strtolower($code); ?>.png" alt="<?= strtoupper($code); ?>" class="flag-icon">
									<?= strtoupper($code); ?>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</header>

<main class="site-main">
	<div class="main-inner container">
		<div class="home-layout">
			<aside class="home-left">
				<section class="panel user-panel-box">
					<div class="panel__header">
						<span><?= t('user_panel'); ?></span>
					</div>

					<div class="panel__body">
						<?php if ($loggedIn): ?>
							<div class="user-welcome">
								<p><?= t('welcome') ?> <strong><?= htmlspecialchars($_SESSION['account_login'], ENT_QUOTES, 'UTF-8'); ?></strong></p>
							</div>
							<nav class="user-panel-nav">
								<a href="<?= $baseUrl; ?>/user/panel" class="btn"><?= t('user_panel'); ?></a>
								<?php if (!empty($socialLinks['ishop'])): ?>
									<a href="<?= $socialLinks['ishop']; ?>" class="btn"><?= t('item_shop'); ?></a>
								<?php endif; ?>

								<?php if (!empty($_SESSION['account_web_admin']) && (int)$_SESSION['account_web_admin'] >= 1): ?>
									<a href="<?= $baseUrl; ?>/admin" class="btn"><?= t('administration'); ?></a>
								<?php endif; ?>
								<a href="<?= $baseUrl; ?>/logout" class="btn"><?= t('logout'); ?></a>
							</nav>
						<?php else: ?>
							<form method="post" action="<?= $baseUrl; ?>/login">
								<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
								
								<div class="form-group">
									<input class="form-input" type="text" name="username" placeholder="<?= t('user_name'); ?>" required>
								</div>

								<div class="form-group">
									<input class="form-input" type="password" name="password" placeholder="<?= t('password'); ?>" required>
								</div>

								<?php if ($recaptchaEnabled && $recaptchaSiteKey): ?>
									<div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($recaptchaSiteKey); ?>"></div>
									<script src="https://www.google.com/recaptcha/api.js" async defer></script>
								<?php endif; ?>

								<button type="submit" class="btn-login">
									<?= t('login'); ?>
								</button>
							</form>

							<div class="form-links">
								<a href="<?= $baseUrl; ?>/forgot-password"><?= t('forgot_password'); ?></a>
							</div>
						<?php endif; ?>
					</div>
				</section>

				<?php if ($hasAnyStatEnabled && isset($stats)): ?>
					<section class="panel stats-box">
						<div class="panel__header">
							<span><?= t('stats.title', 'Statistici server'); ?></span>
						</div>

						<div class="panel__body">
							<ul class="stats-list">
								<?php if ($showPlayersOnline): ?>
									<li>
										<span><?= t('stats.players_online'); ?></span>
										<strong><?= (int)($stats['players_online'] ?? 0); ?></strong>
									</li>
								<?php endif; ?>

								<?php if ($showPlayers24h): ?>
									<li>
										<span><?= t('stats.players_24h'); ?></span>
										<strong><?= (int)($stats['players_24h'] ?? 0); ?></strong>
									</li>
								<?php endif; ?>

								<?php if ($showAccountsTotal): ?>
									<li>
										<span><?= t('stats.accounts_created'); ?></span>
										<strong><?= (int)($stats['accounts_total'] ?? 0); ?></strong>
									</li>
								<?php endif; ?>

								<?php if ($showCharactersTotal): ?>
									<li>
										<span><?= t('stats.characters_created'); ?></span>
										<strong><?= (int)($stats['characters_total'] ?? 0); ?></strong>
									</li>
								<?php endif; ?>

								<?php if ($showGuildsTotal): ?>
									<li>
										<span><?= t('stats.guilds_created'); ?></span>
										<strong><?= (int)($stats['guilds_total'] ?? 0); ?></strong>
									</li>
								<?php endif; ?>
							</ul>
						</div>
					</section>
				<?php endif; ?>
			</aside>

			<section class="home-center">
				<!--
				<?php if ($isHomePage): ?>
					<section class="panel hero-banner">
						<div class="panel__body">
							<div class="hero-image-placeholder">
								BANNER / SLIDER
							</div>
						</div>
					</section>
				<?php endif; ?>
				-->

				<section class="panel page-content-wrapper">
					<?php if (!empty($title)): ?>
						<div class="panel__header">
							<h1 class="page-title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
						</div>
					<?php endif; ?>

					<div class="panel__body">
						<?php require $pagePath; ?>
					</div>
				</section>
			</section>

			<aside class="home-right">
				<?php if (!empty($topPlayers)): ?>
					<section class="panel ranking-panel-box">
						<div class="panel__header">
							<span><?= t('top_players_short'); ?></span>
						</div>

						<div class="panel__body">
							<ol class="top-ranking-list">
								<?php foreach ($topPlayers as $p): ?>
									<li class="ranking-entry">
										<span class="player-name"><?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?></span>
										<span class="player-level">Lv. <?= (int)$p['level']; ?></span>
									</li>
								<?php endforeach; ?>
							</ol>
						</div>
					</section>
				<?php endif; ?>

				<?php if (!empty($topGuilds)): ?>
					<section class="panel ranking-panel-box">
						<div class="panel__header">
							<span><?= t('top_guilds_short'); ?></span>
						</div>

						<div class="panel__body">
							<ol class="top-ranking-list">
								<?php foreach ($topGuilds as $g): ?>
									<li class="ranking-entry">
										<span class="guild-name"><?= htmlspecialchars($g['name'], ENT_QUOTES, 'UTF-8'); ?></span>
										<span class="guild-level">Lv. <?= (int)$g['level']; ?></span>
									</li>
								<?php endforeach; ?>
							</ol>
						</div>
					</section>
				<?php endif; ?>
			</aside>
		</div>
	</div>
</main>

<footer class="site-footer">
	<div class="footer-inner container">
		<?php if (!empty($pages)): ?>
			<?php
			$limitedPages = array_slice($pages, 0, 6);
			$leftPages  = [];
			$rightPages = [];

			foreach ($limitedPages as $index => $page) {
				if ($index % 2 === 0) {
					$leftPages[]  = $page;
				} else {
					$rightPages[] = $page;
				}
			}
			?>

			<div class="footer-column footer-left-col">
				<?php foreach ($leftPages as $pageItem): ?>
					<a href="<?= $baseUrl; ?>/page?slug=<?= htmlspecialchars($pageItem['slug'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> <?= htmlspecialchars($pageItem['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?> </a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="footer-copyright">
			<p>&copy; <?= date('Y'); ?> <?= $siteName; ?> All rights reserved</p>
		</div>

		<?php if (!empty($pages)): ?>
			<div class="footer-column footer-right-col">
				<?php foreach ($rightPages as $pageItem): ?>
					<a href="<?= $baseUrl; ?>/page?slug=<?= htmlspecialchars($pageItem['slug'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> <?= htmlspecialchars($pageItem['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?> </a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</footer>

<script src="<?= $assetsUrl; ?>/js/main.js?v=3.7"></script>
</body>
</html>
