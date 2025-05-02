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

// Ha az űrlapot elküldték (értékelést beküldték)
if(isset($_POST['submit'])) {
    if($is_logged_in) { // Ellenőrzi, hogy a felhasználó be van-e jelentkezve
        $username = $_SESSION['username']; // Felhasználónév
        // Ellenőrizzük, hogy ki lett-e választva értékelés
        if(isset($_POST['ertekeles'])) {
            $ertekeles = $_POST['ertekeles']; // Az értékelés

            // Az értékelések betöltése a JSON fájlból
            $erteklesek = json_decode(file_get_contents('JSON/erteklesek.json'), true);

            // Felülírja az előző értékelést, ha a felhasználó már értékelt
            $erteklesek[$username] = $ertekeles;

            // Az értékelések mentése JSON fájlba
            file_put_contents('JSON/erteklesek.json', json_encode($erteklesek));
        }
    }
}

// Az összes értékelés átlagának kiszámítása
$erteklesek = json_decode(file_get_contents('JSON/erteklesek.json'), true);
$osszesErtekeles = array_values($erteklesek);
$atlageredmeny = count($osszesErtekeles) > 0 ? array_sum($osszesErtekeles) / count($osszesErtekeles) : 0;
?>
<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konda | Főoldal</title>
    <link rel="stylesheet" href="css/css.css">
</head>

<body>
<header>
    <div class="logo">Konda</div>
    <nav class="nav-bar">
        <ul>
            <li>
                <a href="index.php" class="active">Főoldal</a>
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


<div class="fooldal">
    <div class="rolunk">
        <h1>RÓLUNK</h1>
        <p>Üdvözöljük a Konda Fitness hivatalos weboldalán!
            <br> A mi missziónk az, hogy minden ember aki a termünk szolgáltatásait használja kihozza magából mentálisan és fizikálisan is a maximumot.
            <br>Termünket az Arnold era, modern testépítés és a keleti szentélyek nyugalma inspirálta.
            <br>Minden sportolni. mozogni és erősődni vágyó embert szeretettel várunk termünkbe, ahol szuper segítőkész és inspiráló a környezet.
            <br>Termünk széleskörűen felszerelt, hiszen a legmodernebb konditermi felszereléseken kívül a wellness részelgünkön medencék, masszázs, jacuzzi és szauna várja vendégeinket!
            <br>
            A csoportos órák szerelmeseinek is jópár lehetőséget tartogatunk. Spinning, jóga, meditációs terem, funkcionális edzés és önvédelmi órák is vannak nálunk.
        </p>
    </div>

    <div class="nyitvatartas">
        <h2>Nyitvatartás</h2>
        <p><b>Hétfőtől-Péntekig:</b> 0:00-24:00 <br>
            <b>Hétvégén:</b> 04:00-22:00
        </p>
    </div>

    <div class="hol">
        <h2>Hol találja a termet?</h2>
        <p><b>Címünk:</b> Konda fitness, Szolnok Széchenyi István körút 122. A híres barbershop mellett</p>
        <p><iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2711.1706970565665!2d20.184135176795973!3d47.19367141640046!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47414144e0eedf0d%3A0xceee7758e87a9434!2sThe%20Gypsy%20Barbershop!5e0!3m2!1shu!2shu!4v1711113997531!5m2!1shu!2shu"
                   width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></p>
    </div>



    <!-- Értékelés űrlap -->
    <div class="ertekelo">
        <h2>Értékelje a konditermet</h2>
        <form method="post">
            <ul class="ertekelo-sav">
                <?php for($i = 1; $i <= 5; $i++): ?>
                    <li>
                        <input type="radio" id="ertekeles<?php echo $i; ?>" name="ertekeles" value="<?php echo $i; ?>" <?php if(!$is_logged_in) echo "disabled"; ?>>
                        <label for="ertekeles<?php echo $i; ?>"><?php echo $i; ?></label>
                    </li>
                <?php endfor; ?>
            </ul>
            <?php if($is_logged_in): ?>
                <input type="submit" name="submit" value="Küldés">
            <?php endif; ?>
        </form>
    </div>

    <!-- Átlag értékelés -->
    <div class="atlag-ertekeles">
        <?php if(count($osszesErtekeles) > 0): ?>
            <p>A terem értékelése: <?php echo number_format($atlageredmeny, 2); ?>/5</p>
        <?php else: ?>
            <p>Még nem érkezett a teremre értékelés.</p>
        <?php endif; ?>
    </div>







</div>
</body>
</html>
