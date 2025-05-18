<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/User.php';

$user = User::fromSession();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="<?= PREFIX ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= PREFIX ?>/css/custom.css">


    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" as="style">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?display=swap&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900&amp;family=Work+Sans%3Awght%40400%3B500%3B700%3B900" />
</head>

<body class="d-flex flex-column min-vh-100 bg-light">
    <header class="d-flex justify-content-between align-items-center border-bottom border-light px-4 py-3 bg-white">
        <a href="<?= PREFIX ?>/index.php" class="d-flex align-items-center gap-3 text-dark text-decoration-none">
            <i class="fa-solid fa-truck-moving fs-3"></i>
            <span class="fw-bold h4 mb-0 ms-2" style="line-height:1;">Bahrent</span>
        </a>
        <div class="d-flex justify-content-end align-items-center gap-2">
            <?php if (!$user): ?>
                <a href="<?= PREFIX ?>/login.php" class="btn d-flex align-items-center justify-content-center h-100 px-4 bg-success text-sm font-weight-bold rounded-lg text-white hover-bg-success">Log in</a>
                <a href="<?= PREFIX ?>/signup.php" class="btn d-flex align-items-center justify-content-center h-100 px-4 bg-light text-sm font-weight-bold rounded-lg text-dark hover-bg-light">Sign up</a>
            <?php else: ?>

                <?php if ($user->role === 'admin'): ?>
                    <a href="<?= PREFIX ?>/admin.php" class="btn d-flex align-items-center justify-content-center px-3 h-100 text-sm font-weight-bold rounded-lg hover-bg-light">Admin Dashboard</a>
                <?php elseif ($user->role === 'homeowner'): ?>
                    <a href="<?= PREFIX ?>/homeowner.php" class="btn d-flex align-items-center justify-content-center px-3 h-100 text-sm font-weight-bold rounded-lg hover-bg-light">Homeowner Dashboard</a>
                <?php else: ?>
                    <a href="<?= PREFIX ?>/booking.php" class="btn d-flex align-items-center justify-content-center px-3 h-100 text-sm font-weight-bold rounded-lg hover-bg-light">My Bookings</a>
                <?php endif; ?>
                <a href="<?= PREFIX ?>/profile.php" class="btn fs-2 d-flex h-100 text-center align-items-center justify-content-center">
                    <i class="fa-solid fa-circle-user text-3xl"></i>
                </a>
            <?php endif; ?>
        </div>
    </header>

    <main role="main" class="flex-fill d-flex flex-column max-w-screen-lg mx-auto w-100 overflow-hidden px-4">
        <?= $content ?>
    </main>

</body>

<script src="<?= PREFIX ?>/js/bootstrap.min.js"></script>

</html>