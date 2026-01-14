<article class="page-single">
	<div class="page-content">
		<?= $page['content']; ?>
	</div>

	<?php if (!empty($page['created_at'])): ?>
		<div class="page-footer">
			<span class="page-date">
				<?= htmlspecialchars($page['created_at'], ENT_QUOTES, 'UTF-8'); ?>
			</span>
		</div>
	<?php endif; ?>
</article>
