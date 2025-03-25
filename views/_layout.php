<?php

/** @var string $title */
/** @var string $content */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/custom.css">

    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" as="style">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?display=swap&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900&amp;family=Work+Sans%3Awght%40400%3B500%3B700%3B900" />
</head>

<body class="d-flex flex-column min-vh-100 bg-light">
    <header class="d-flex justify-content-between align-items-center border-bottom border-light px-4 py-3 bg-white">
        <div class="d-flex align-items-center gap-3 text-dark">
            <i class="fa-solid fa-truck-moving fs-3"></i>
            <a class="btn h4 font-weight-bold" href="/">Bahrent</a>
        </div>
        <div class="d-flex justify-content-end align-items-center gap-2">
            <a href="/login.php" class="btn d-flex align-items-center justify-content-center h-100 px-4 bg-success text-sm font-weight-bold rounded-lg text-dark hover-bg-success">Log in</a>
            <a href="/signup.php" class="btn d-flex align-items-center justify-content-center h-100 px-4 bg-light text-sm font-weight-bold rounded-lg text-dark hover-bg-light">Sign up</a>

            <a href="/admindashboard/Index" class="btn d-flex align-items-center justify-content-center px-3 h-100 text-sm font-weight-bold rounded-lg hover-bg-light">Dashboard</a>

            <a href="/trackrental/Index" class="btn d-flex align-items-center justify-content-center px-3 h-100 text-sm font-weight-bold rounded-lg hover-bg-light">My Rentals</a>

            <a href="/userprofile/Index" class="btn fs-2 d-flex h-100 text-center align-items-center justify-content-center">
                <i class="fa-solid fa-circle-user text-3xl"></i>
            </a>
        </div>
    </header>

    <main role="main" class="flex-fill d-flex flex-column max-w-screen-lg mx-auto w-100 overflow-hidden px-4">
        <?= $content ?>
    </main>

</body>

<script src="/js/bootstrap.min.js"></script>

</html>