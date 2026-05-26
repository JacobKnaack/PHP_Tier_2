<?php /** @var array $link */ ?>

<li class="link-item">
    <img 
        src="<?= htmlspecialchars($link['favicon']) ?>" 
        alt="" 
        class="favicon"
        onerror="this.style.display='none'"
    >

    <div class="link-info">
        <a href="<?= htmlspecialchars($link['url']) ?>" target="_blank">
            <?= htmlspecialchars($link['title']) ?>
        </a>
        <span class="domain"><?= htmlspecialchars($link['domain']) ?></span>
    </div>

    <div class="link-actions">
        <?php if (!$link['read']): ?>
            <form action="/links/<?= $link['id'] ?>/read" method="POST">
                <button type="submit" class="mark-read">Mark Read</button>
            </form>
        <?php endif; ?>

        <form action="/links/<?= $link['id'] ?>" method="DELETE" onsubmit="return confirm('Delete this link?')">
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="delete">Delete</button>
        </form>
    </div>
</li>
