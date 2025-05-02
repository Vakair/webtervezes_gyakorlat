<?php
session_start();

$products = array(
    0 => array("name" => "Alap Tagság", "price" => 12000),
    1 => array("name" => "Premium Tagság", "price" => 18000),
    2 => array("name" => "Elite Tagság", "price" => 25000),
    3 => array("name" => "Alap Diák", "price" => 8000),
    4 => array("name" => "Napijegy", "price" => 2000),
    5 => array("name" => "10 alkalmas jegy", "price" => 9000),
    6 => array("name" => "Diák napijegy", "price" => 1500),
    7 => array("name" => "Diák 10 alkalmas jegy", "price" => 3500),
    8 => array("name" => "Felnőtt havi", "price" => 12000),
    9 => array("name" => "10 alkalmas bérlet", "price" => 8000),
    10 => array("name" => "Napijegy", "price" => 2000),
    11 => array("name" => "Diák havi", "price" => 8000),
    12 => array("name" => "Diák 10 alkalmas bérlet", "price" => 3500),
    13 => array("name" => "Diák napijegy", "price" => 1500),
    14 => array("name" => "Medencék, szauna és jaccuzik", "price" => 5000),
    15 => array("name" => "Masszázs", "price" => 4500),
    16 => array("name" => "Diák medencék, szauna és jaccuzik", "price" => 3000),
    17 => array("name" => "Diák masszázs", "price" => 3500),
    18 => array("name" => "Egyéni Edzés", "price" => 3500),
    19 => array("name" => "5 alkalmas Csomag", "price" => 24000),
    20 => array("name" => "10 alkalmas Csomag", "price" => 38000)
);

// Kosár kezelése
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $product_id = $_POST['product_id'];
        if (isset($products[$product_id])) {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity']++;
            } else {
                $_SESSION['cart'][$product_id] = array(
                    'quantity' => 1,
                    'name' => $products[$product_id]['name'],
                    'price' => $products[$product_id]['price']
                );
            }
            $_SESSION['notification'] = 'A(z) ' . $products[$product_id]['name'] . ' sikeresen hozzáadva a kosárhoz!';
        }
    }

    if (isset($_POST['action']) && $_POST['action'] == 'remove') {
        $product_id = $_POST['product_id'];
        if (isset($_SESSION['cart'][$product_id])) {
            if ($_SESSION['cart'][$product_id]['quantity'] > 1) {
                $_SESSION['cart'][$product_id]['quantity']--;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
        }
    }


    if (isset($_POST['action']) && $_POST['action'] == 'save_cart') {
        if (!empty($_SESSION['cart'])) {
            // Az aktuális kosár tartalmának elmentése
            $saved_cart = $_SESSION['cart'];

            // Az előzőleg elmentett kosár tartalmának beolvasása, ha létezik
            $prev_saved_cart = array();
            if (file_exists('../JSON/saved_cart.json')) {
                $prev_saved_cart = json_decode(file_get_contents('JSON/saved_cart.json'), true);
            }

            // Az elmentett kosár tartalmának frissítése az aktuális kosárral
            foreach ($saved_cart as $item) {
                $prev_saved_cart[] = $item;
            }

            // A mentett kosár tartalmának kiírása a fájlba
            $json_data = json_encode($prev_saved_cart, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            file_put_contents('JSON/saved_cart.json', $json_data);
        }

        // A kosár ürítése
        $_SESSION['cart'] = array();
    }
}

// Végösszeg számítása és megjelenítése
$total_price = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $product) {
        $total_price += $product['price'] * $product['quantity'];
    }
}

$afa = $total_price * 0.05; // Példa adószámítás
$vegso_osszeg = $total_price + $afa;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_cart') {
    $_SESSION['saved_cart'] = $_SESSION['cart'];
}


?>

<?php
// Ellenőrzi, hogy a felhasználó be van-e jelentkezve
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
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/css.css">
    <link rel="stylesheet" href="css/services.css">
    <title>Konda | Szolgáltatások</title>
</head>
<body>

<?php
// Értesítés megjelenítése, ha van
if (isset($_SESSION['notification'])) {
    echo '<div class="notification">' . $_SESSION['notification'] . '</div>';

    // Értesítés törlése, hogy ne jelenjen meg újra
    unset($_SESSION['notification']);
}
?>

<header>
    <div class="logo">Konda</div>
    <nav class="nav-bar">
        <ul>
            <li>
                <a href="index.php">Főoldal</a>
            </li>
            <li>
                <a href="service1.php" class="active">Szolgáltatások</a>
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






<div class="container">
    <h1>Szolgáltatások</h1>

    <h2>Tagságok</h2>
    <table>
        <tr>
            <th>Tagság Típusa</th>
            <th>Ár</th>
            <th>Leírás</th>
            <th></th>
        </tr>
        <tr>
            <td>Alap Tagság</td>
            <td>12000Ft/hónap</td>
            <td>Az összes konditermi felszerelést használhatod vele.</td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="product_id" value="0"> <!-- Az alap tagság azonosítója -->
                    <input type="hidden" name="product_name" value="Alap Tagság">
                    <input type="hidden" name="action" value="add">
                    <button class="add-to-cart" type="submit">Kosárba</button>
                </form>
            </td>
        </tr>
        <tr>
            <td>Premium Tagság</td>
            <td>18000Ft/hónap</td>
            <td>Az összes konditermi felszerelést használhatod vele. Emellett a szanuát, az úszómedencét, a termálvizesmedencét és a jacuzzikat is igénybeveheted.</td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="product_id" value="1"> <!-- Az alap tagság azonosítója -->
                    <input type="hidden" name="product_name" value="Premium Tagság">
                    <input type="hidden" name="action" value="add">
                    <button class="add-to-cart" type="submit">Kosárba</button>
                </form>
            </td>
        </tr>
        <tr>
            <td>Elite Tagság</td>
            <td>25000Ft/hónap</td>
            <td>Az összes konditermi felszerelést használhatod vele. Emellett a szanuát, az úszómedencét, a termálvizesmedencét és a jacuzzikat is igénybeveheted. Ráadásként az összes csoportos órát látogathatod és egy darab ingyen masszázst is kapsz a bérleted mellé.</td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="product_id" value="2"> <!-- Az alap tagság azonosítója -->
                    <input type="hidden" name="product_name" value="Elite Tagság">
                    <input type="hidden" name="action" value="add">
                    <button class="add-to-cart" type="submit">Kosárba</button>
                </form>
            </td>
        </tr>
        <tr>
            <td>Alap Diák</td>
            <td>8000Ft/hónap</td>
            <td>Az összes konditermi felszerelést használhatod vele.</td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="product_id" value="3"> <!-- Az alap tagság azonosítója -->
                    <input type="hidden" name="product_name" value="Alap Diák">
                    <input type="hidden" name="action" value="add">
                    <button class="add-to-cart" type="submit">Kosárba</button>
                </form>
            </td>
        </tr>
    </table>

    <h2>Belépők</h2>
    <table>
        <tr>
            <th>Jegy Típusa</th>
            <th>Ár</th>
            <th>Leírás</th>
            <th></th>
        </tr>
        <tr>
            <td>Napijegy</td>
            <td>2000Ft</td>
            <td>1 alkalommal használhatod a konditermi felszereléseket</td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="product_id" value="4"> <!-- Az alap tagság azonosítója -->
                    <input type="hidden" name="product_name" value="Napijegy">
                    <input type="hidden" name="action" value="add">
                    <button class="add-to-cart" type="submit">Kosárba</button>
                </form>
            </td>
        </tr>
        <tr>
            <td>10 alkalmas jegy</td>
            <td>9000Ft</td>
            <td>10 alkalommal használhatod a konditermi felszereléseket</td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="product_id" value="5"> <!-- Az alap tagság azonosítója -->
                    <input type="hidden" name="product_name" value="10 alkalmas jegy">
                    <input type="hidden" name="action" value="add">
                    <button class="add-to-cart" type="submit">Kosárba</button>
                </form>
            </td>
        </tr>
        <tr>
            <td>Diák napijegy</td>
            <td>1500Ft</td>
            <td>1 alkalommal használhatod a konditermi felszereléseket</td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="product_id" value="6"> <!-- Az alap tagság azonosítója -->
                    <input type="hidden" name="product_name" value="Diák napijegy">
                    <input type="hidden" name="action" value="add">
                    <button class="add-to-cart" type="submit">Kosárba</button>
                </form>
            </td>
        </tr>
        <tr>
            <td>Diák 10 alkalmas jegy</td>
            <td>3500Ft</td>
            <td>10 alkalommal használhatod a konditermi felszereléseket</td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="product_id" value="7"> <!-- Az alap tagság azonosítója -->
                    <input type="hidden" name="product_name" value="Diák 10 alkalmas jegy">
                    <input type="hidden" name="action" value="add">
                    <button class="add-to-cart" type="submit">Kosárba</button>
                </form>
            </td>
        </tr>
    </table>

    <h2>Csoportos órák</h2>
    <table>
        <tr>
            <th>Jegy Típusa</th>
            <th>Ár</th>
            <th>Leírás</th>
            <th></th>
        </tr>
        <tr>
            <td>Felnőtt havi</td>
            <td>12000Ft</td>
            <td>Bármelyik csoportos órán résztvehetsz</td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="product_id" value="8"> <!-- Az alap tagság azonosítója -->
                    <input type="hidden" name="product_name" value="Felnőtt havi">
                    <input type="hidden" name="action" value="add">
                    <button class="add-to-cart" type="submit">Kosárba</button>
                </form>
            </td>
        </tr>
        <tr>
            <td>10 alkalmas bérlet</td>
            <td>8000Ft</td>
            <td>10 alkalomszor a csoportos órák bármelyikén résztvehetsz</td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="product_id" value="9"> <!-- Az alap tagság azonosítója -->
                    <input type="hidden" name="product_name" value="10 alkalmas bérlet">
                    <input type="hidden" name="action" value="add">
                    <button class="add-to-cart" type="submit">Kosárba</button>
                </form>
            </td>
        </tr>
        <tr>
            <td>Napijegy</td>
            <td>2000Ft</td>
            <td>Részt vehetsz egy darab csoportos órán</td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="product_id" value="10"> <!-- Az alap tagság azonosítója -->
                    <input type="hidden" name="product_name" value="Napijegy">
                    <input type="hidden" name="action" value="add">
                    <button class="add-to-cart" type="submit">Kosárba</button>
                </form>
            </td>
        </tr>

        <tr>
            <td>Diák havi</td>
            <td>8000Ft</td>
            <td>Bármelyik csoportos órán résztvehetsz</td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="product_id" value="11"> <!-- Az alap tagság azonosítója -->
                    <input type="hidden" name="product_name" value="Diák havi">
                    <input type="hidden" name="action" value="add">
                    <button class="add-to-cart" type="submit">Kosárba</button>
                </form>
            </td>
        </tr>
        <tr>
            <td>Diák 10 alkalmas bérlet</td>
            <td>3500Ft</td>
            <td>10 alkalomszor a csoportos órák bármelyikén résztvehetsz</td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="product_id" value="12"> <!-- Az alap tagság azonosítója -->
                    <input type="hidden" name="product_name" value="Diák 10 alkalmas bérlet">
                    <input type="hidden" name="action" value="add">
                    <button class="add-to-cart" type="submit">Kosárba</button>
                </form>
            </td>
        </tr>
        <tr>
            <td>Diák napijegy</td>
            <td>1500Ft</td>
            <td>Részt vehetsz egy darab csoportos órán</td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="product_id" value="13"> <!-- Az alap tagság azonosítója -->
                    <input type="hidden" name="product_name" value="Diák napijegy">
                    <input type="hidden" name="action" value="add">
                    <button class="add-to-cart" type="submit">Kosárba</button>
                </form>
            </td>
        </tr>
    </table>

    <h2>Wellness</h2>
    <table>
        <tr>
            <th>Jegy Típusa</th>
            <th>Ár</th>
            <th>Leírás</th>
            <th></th>
        </tr>
        <tr>
            <td>Medencék, szauna és jaccuzik</td>
            <td>5000Ft</td>
            <td>Bármelyik medencét jacuzzit vagy szaunát használhatod ezzel a belépővel</td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="product_id" value="14"> <!-- Az alap tagság azonosítója -->
                    <input type="hidden" name="product_name" value="Medencék, szauna és jaccuzik">
                    <input type="hidden" name="action" value="add">
                    <button class="add-to-cart" type="submit">Kosárba</button>
                </form>
            </td>
        </tr>
        <tr>
            <td>Masszázs</td>
            <td>4500Ft</td>
            <td>Stressz felszabadító thai masszázs</td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="product_id" value="15"> <!-- Az alap tagság azonosítója -->
                    <input type="hidden" name="product_name" value="Masszázs">
                    <input type="hidden" name="action" value="add">
                    <button class="add-to-cart" type="submit">Kosárba</button>
                </form>
            </td>
        </tr>
        <tr>
            <td>Diák medencék, szauna és jaccuzik</td>
            <td>3000Ft</td>
            <td>Bármelyik medencét jacuzzit vagy szaunát használhatod ezzel a belépővel</td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="product_id" value="16"> <!-- Az alap tagság azonosítója -->
                    <input type="hidden" name="product_name" value="Diák medencék, szauna és jaccuzik">
                    <input type="hidden" name="action" value="add">
                    <button class="add-to-cart" type="submit">Kosárba</button>
                </form>
            </td>
        </tr>
        <tr>
            <td>Diák masszázs</td>
            <td>3500Ft</td>
            <td>Stressz felszabadító thai masszázs</td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="product_id" value="17"> <!-- Az alap tagság azonosítója -->
                    <input type="hidden" name="product_name" value="Diák masszázs">
                    <input type="hidden" name="action" value="add">
                    <button class="add-to-cart" type="submit">Kosárba</button>
                </form>
            </td>
        </tr>
    </table>

    <h2>Személyi Edzés</h2>
    <table>
        <tr>
            <th>Szolgáltatás Típusa</th>
            <th>Ár</th>
            <th>Leírás</th>
            <th></th>
        </tr>
        <tr>
            <td>Egyéni Edzés</td>
            <td>3500Ft/óra</td>
            <td>1 órás személyi edzési időpont</td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="product_id" value="18"> <!-- Az alap tagság azonosítója -->
                    <input type="hidden" name="product_name" value="Egyéni Edzés">
                    <input type="hidden" name="action" value="add">
                    <button class="add-to-cart" type="submit">Kosárba</button>
                </form>
            </td>
        </tr>
        <tr>
            <td>5 alkalmas Csomag</td>
            <td>24000Ft</td>
            <td>5 alkalmas személyi edzéscsomag</td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="product_id" value="19"> <!-- Az alap tagság azonosítója -->
                    <input type="hidden" name="product_name" value="5 alkalmas Csomag">
                    <input type="hidden" name="action" value="add">
                    <button class="add-to-cart" type="submit">Kosárba</button>
                </form>
            </td>
        </tr>
        <tr>
            <td>10 alkalmas Csomag</td>
            <td>38000Ft</td>
            <td>10 alkalmas személyi edzéscsomag</td>
            <td>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="product_id" value="20"> <!-- Az alap tagság azonosítója -->
                    <input type="hidden" name="product_name" value="10 alkalmas személyi edzéscsomag">
                    <input type="hidden" name="action" value="add">
                    <button class="add-to-cart" type="submit">Kosárba</button>
                </form>
            </td>
        </tr>
    </table>

    <h2>Egyéb szolgáltatások</h2>
    <table>
        <tr>
            <th>Szolgáltatás Típusa</th>
            <th>Ár</th>
            <th>Leírás</th>
        </tr>
        <tr>
            <td>Törölköző</td>
            <td >200Ft</td>
            <td>Törölköző bérlése a pultnál igényelhető</td>
        </tr>
    </table>
</div>
</body>
</html>