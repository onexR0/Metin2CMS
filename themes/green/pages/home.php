<?php if (!empty($news)): ?>
	<div class="news-list">
		<?php foreach ($news as $n): ?>
			<article class="news-item">
				<div class="news-title-wrapper">
					<h2><?= htmlspecialchars($n['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
				</div>

				<div class="news-content">
					<?= $n['content']; ?>
				</div>

				<div class="news-footer">
					<span class="news-date">
						<?= htmlspecialchars($n['created_at'], ENT_QUOTES, 'UTF-8'); ?>
					</span>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
