<?php
// Ellenőrzi, hogy a felhasználó be van-e jelentkezve
session_start();
$is_logged_in = isset($_SESSION['username']);
$is_logged_in_ADMIN = isset($_SESSION['username']) && $_SESSION['username'] === 'ADMIN';

// Kijelentkezés folyamat
if(isset($_GET['logout'])) {
    // Töröljük a bejelentkezett felhasználó adatait a session-ből
    session_unset();
    session_destroy();
    // Átirányítás a főoldalra
    header("Location: index.php");
    exit;
}

// Ellenőrizzük, hogy az adminisztrátor be van-e jelentkezve
if(!$is_logged_in_ADMIN) {
    // Ha nem adminisztrátor, átirányítjuk őket a főoldalra vagy valamilyen hibaüzenetet jelenítünk meg
    header("Location: index.php");
    exit;
}
?>



<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konda | Admin</title>
    <link rel="stylesheet" href="css/css.css">
</head>

<body>
<header>
    <div class="logo">Konda</div>
    <nav class="nav-bar">
        <ul>
            <li>
                <a href="index.php">Főoldal</a>
            </li>
            <li>
                <a href="service1.php">Szolgáltatások</a>
            </li>
            <li>
                <a href="cart.php">Kosár</a>
            </li>
            <li>
                <a href="profil.php">Profil</a>
            </li>
            <?php if($is_logged_in): ?>
                <li>
                    <a href="?logout">Kijelentkezés</a>
                </li>
            <?php else: ?>
                <li>
                    <a href="login.php">Bejelentkezés</a>
                </li>
            <?php endif; ?>

            <?php if($is_logged_in_ADMIN): ?>
                <li>
                    <a href="admin.php">Admin</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</header>



    <div class="admin">
        <h1>Admin</h1>
        <a href="#" class="button">Szolgáltatások módosítása</a>
        <br>
        <a href="#" class="button">Új szolgáltatás hozzáadása</a>
    </div>


</body>
</html>