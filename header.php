<?php
declare(strict_types=1);

$pageTitle = $pageTitle ?? APP_NAME;
$flash = get_flash();
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= e(app_url('assets/css/style.css?v=20261002')) ?>" rel="stylesheet">
</head>
<body class="bg-light">
<?php require __DIR__ . '/navbar.php'; ?>
<main class="container py-4">
    <?php if (!empty($celebrate)): ?><div class="celebration-layer" aria-label="Congratulations"><div class="firework firework-one"></div><div class="firework firework-two"></div><div class="firework firework-three"></div><div class="firework firework-four"></div><div class="firework firework-five"></div><div class="celebration-message">Congratulations! Great work!</div><div class="confetti-container"></div></div><?php endif; ?>
    <?php if ($flash && $flash['type'] === 'login'): ?>
        <div class="modal fade" id="loginRequiredModal" tabindex="-1" aria-labelledby="loginRequiredTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h2 class="modal-title h5" id="loginRequiredTitle">Login required</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><?= e($flash['message']) ?></div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button><a class="btn btn-primary" href="<?= e(app_url('auth/login.php')) ?>">Please log in</a></div></div></div>
        </div>
    <?php elseif ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
