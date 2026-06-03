<?php
/** @var array $shortlink */
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$shortUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . '/s/' . $shortlink['code'];
?>

<h1>Short Link Created</h1>

<p>Your short link is ready:</p>

<div class="shortlink-box">
    <input type="text" value="<?= htmlspecialchars($shortUrl) ?>" readonly>
    <button onclick="copyShortlink()">Copy</button>
</div>

<p>
    <a href="/links/<?= $shortlink['link_id'] ?>/stats">View Analytics</a> |
    <a href="/inbox-list">Back to Inbox</a>
</p>

<script>
function copyShortlink() {
    const input = document.querySelector('.shortlink-box input');
    input.select();
    document.execCommand('copy');
    alert('Copied to clipboard');
}
</script>

<style>
.shortlink-box {
    display: flex;
    gap: 8px;
    margin: 12px 0;
}

.shortlink-box input {
    flex: 1;
    padding: 8px;
    font-size: 1rem;
}
</style>
