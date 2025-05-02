<?php
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

// Ellenőrizzük, hogy be van-e jelentkezve a felhasználó
if ($is_logged_in) {
    // Az adott felhasználó nevét és jelszavát lekérjük a session-ből
    $username = $_SESSION['username'];
    // Felhasználó adatainak lekérése az adatforrásból (pl. adatbázis vagy JSON fájl)
    $users = json_decode(file_get_contents('JSON/users.json'));
    if ($users !== null && isset($users->$username)) {
        $user_data = $users->$username;
        $fullname = $user_data->fullname;
        $password_length = strlen($user_data->password);
        $hidden_password = str_repeat("*", $password_length); // Jelszó kicsillagozása
        $email = $user_data->email;
        $age = $user_data->age;
        $discount = $user_data->discount;
        $student_id = $user_data->student_id;
    } else {
        // Hibás felhasználónév
        $error_message = 'Hibás felhasználónév!';
    }
} else {
    // Ha nincs bejelentkezett felhasználó, átirányítás a bejelentkezési oldalra
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konda | Profil</title>
    <link rel="stylesheet" href="css/profil.css">
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
                <a href="profil.php" class="active">Profil</a>
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


<main>
    <section class="kep">
        <img src="img/userprofile.jpg" alt="profilkép">
    </section>
    <section class="info">
        <h3><?php echo $fullname; ?></h3>
        <table class="tablazat">
            <tr>
                <td>Felhasználónév: </td>
                <td><?php echo $username; ?></td>
            </tr>
            <tr>
                <td>Jelszó: </td>
                <td><?php echo $hidden_password; ?></td>
            </tr>
            <tr>
                <td>E-mail cím: </td>
                <td><?php echo $email; ?></td>
            </tr>
            <tr>
                <td>Életkor: </td>
                <td><?php echo $age; ?></td>
            </tr>
            <tr>
                <td>Kedvezmények: </td>
                <td><?php echo $discount; ?></td>
            </tr>
            <?php if (isset($_SESSION['discount']) && $_SESSION['discount'] == 'Diák') : ?>
                <tr>
                    <td style="white-space: nowrap">Diákigazolvány száma: </td>
                    <td><?php echo $student_id; ?></td>
                </tr>
            <?php endif; ?>
        </table>
        <section class="szerkesztes">
            <form action="profil_edit.php" method="get">
                <button type="submit" name="edit" class="gomb">Adatok módosítás</button>
                <button type="submit" name="delete" class="delete">Profil törlése</button>
            </form>
        </section>
    </section>





    <section class="aktiv">
        <h3>Érvényben lévő jegyek</h3>
        <section>
            <div>
                <table class="table">
                    <tr>
                        <th>Mettől</th>
                        <th>Meddig</th>
                        <th style="white-space: nowrap">Jegy típusa</th>
                    </tr>
                    <tr>
                        <td>2024.03.15.</td>
                        <td>2024.04.15.</td>
                        <td style="white-space: nowrap">Diák havi bérlet</td>
                    </tr>
                </table>
            </div>
        </section>
    </section>
    <section class="rendelesek">
        <h3>Előző rendelések</h3>
        <div>
            <table class="table">
                <?php
                // Beolvasás a saved_cart.json fájlból
                $saved_cart = json_decode(file_get_contents('JSON/saved_cart.json'), true);

                // Ellenőrzés, hogy van-e tartalom a fájlban
                $elozo_rendeles = false;
                foreach ($saved_cart as $item) {
                    // Ellenőrizd, hogy az adott elem a bejelentkezett felhasználóhoz tartozik-e
                    if ($item['username'] == $_SESSION['username']) {
                        $elozo_rendeles = true;
                        break;
                    }
                }

                // Ha vannak előző rendelések, akkor jelenítsd meg a táblázat fejlécét
                if ($elozo_rendeles) {
                    ?>
                    <tr>
                        <th>Mennyiség</th>
                        <th>Szolgáltatás</th>
                        <th>Ár</th>
                        <th>Vásárlás dátuma</th>
                        <th></th>
                    </tr>
                    <?php
                } else {
                    // Ha nincsenek előző rendelések, akkor írd ki a megfelelő üzenetet
                    echo "<tr><th colspan='1'>Nincsenek előző rendelések</th></tr>";
                }
                ?>

                <?php
                // Ha vannak előző rendelések, akkor jelenítsd meg azokat a táblázatban
                if ($elozo_rendeles) {
                    foreach ($saved_cart as $item) {
                        // Ellenőrizd, hogy az adott elem a bejelentkezett felhasználóhoz tartozik-e
                        if ($item['username'] == $_SESSION['username']) {
                            echo "<tr>";
                            echo "<td>{$item['quantity']}</td>";
                            echo "<td>{$item['name']}</td>";
                            echo "<td>{$item['price']} Ft</td>";
                            echo "<td>{$item['purchase_date']}</td>";
                            echo "<td><button type='button' class='buttonbuy'>Újravásárlás</button></td>";
                            echo "</tr>";
                        }
                    }
                }
                ?>
            </table>
        </div>
    </section>





</main>
</body>
</html>