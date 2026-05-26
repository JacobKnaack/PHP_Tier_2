<?php /* @var $content string */ ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Personal Link Inbox</title>
    <link rel="stylesheet" href="/assets/styles/inbox.css">
</head>
<body>
    <div id="loading-overlay">
        <div class="spinner"></div>
    </div>

    <main>
        <?= $content ?? '' ?>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const overlay = document.getElementById('loading-overlay');

            // Show loading overlay on ANY form submit
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', () => {
                    overlay.style.display = 'flex';
                });
            });
        });
    </script>

</body>
</html>
