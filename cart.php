<?php
session_start();

// Ellenőrzi, hogy a felhasználó be van-e jelentkezve
$is_logged_in = isset($_SESSION['username']);

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
                    'price' => $products[$product_id]['price'],
                    'username' => $_SESSION['username']
                );
            }
        }
    }

    if (isset($_POST['action']) && $_POST['action'] == 'remove') {
        $product_id = $_POST['product_id'];
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]); // Az unset függvény eltávolítja az adott terméket a kosárból
        }
    }

    // Plusz és mínusz gombok kezelése a mennyiség állításához
    if (isset($_POST['action']) && ($_POST['action'] == 'add_quantity' || $_POST['action'] == 'subtract_quantity')) {
        $product_id = $_POST['product_id'];
        if (isset($_SESSION['cart'][$product_id])) {
            if ($_POST['action'] == 'add_quantity') {
                $_SESSION['cart'][$product_id]['quantity']++;
            } elseif ($_POST['action'] == 'subtract_quantity' && $_SESSION['cart'][$product_id]['quantity'] > 1) {
                $_SESSION['cart'][$product_id]['quantity']--;
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_cart') {
        if($is_logged_in) {
            // Az aktuális kosár tartalmának elmentése
            $saved_cart = $_SESSION['cart'];

            $purchase_date = date("Y-m-d H:i:s");

            // Az előzőleg elmentett kosár tartalmának beolvasása, ha létezik
            $prev_saved_cart = array();
            if (file_exists('JSON/saved_cart.json')) {
                $prev_saved_cart = json_decode(file_get_contents('JSON/saved_cart.json'), true);
            }

            foreach ($saved_cart as $item) {
                $item['purchase_date'] = $purchase_date; // Vásárlás dátumának hozzáadása
                $item['username'] = $_SESSION['username'];
                $prev_saved_cart[] = $item;
            }

            // A mentett kosár tartalmának kiírása a fájlba
            $json_data = json_encode($prev_saved_cart, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            file_put_contents('JSON/saved_cart.json', $json_data);

            // A kosár ürítése
            $_SESSION['cart'] = array();
        } else {
            // Hibaüzenet megjelenítése, ha a felhasználó nincs bejelentkezve
            $_SESSION['pay_error'] = "Először jelentkezzen be!";
        }
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konda | Kosár</title>
    <link rel="stylesheet" href="css/profil.css">
</head>

<body>

<?php
if(isset($_SESSION['pay_error'])) {
    echo "<div class='notification'>" . $_SESSION['pay_error'] . "</div>";
    unset($_SESSION['pay_error']);
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
                <a href="service1.php">Szolgáltatások</a>
            </li>
            <li>
                <a href="cart.php" class="active">Kosár</a>
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
            <li>
                <a href="admin.php">Admin</a>
            </li>
        </ul>
    </nav>
</header>

<main class="kosar">
    <section class="items">
        <h2>Kosár tartalma</h2>
        <ul>
            <?php
            if (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $product_id => $product) {
                    echo "<li class='termek'>";
                    echo "<div class='leiras'>";
                    echo "<h3>" . $product['name'] . "</h3>";
                    echo "<p>" . $product['quantity'] . " darab</p>";
                    echo "<span>" . $product['price'] . " Ft</span>";
                    echo "</div>";
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='product_id' value='$product_id'>";
                    echo "<input type='hidden' name='action' value='remove'>";

                    // Plusz és mínusz gombok bal oldalra helyezése a Törlés gombhoz
                    echo "<div>";
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='product_id' value='$product_id'>";
                    echo "<input type='hidden' name='action' value='add_quantity'>";
                    echo "<button type='submit' class='plus_button'>+</button>";
                    echo "</form>";

                    echo "<form method='post'>";
                    echo "<input type='hidden' name='product_id' value='$product_id'>";
                    echo "<input type='hidden' name='action' value='subtract_quantity'>";
                    echo "<button type='submit' class='minus_button'>-</button>";
                    echo "</form>";
                    echo "</div>"; // quantity_buttons
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='product_id' value='$product_id'>";
                    echo "<button type='submit' name='action' value='remove' class='button'>Törlés</button>";
                    echo "</form>";
                    echo "</li>";
                }
            } else {
                echo "<li>A kosár jelenleg üres.</li>";
            }
            ?>
        </ul>
    </section>

    <section class="price">
        <h2>Végösszeg</h2>
        <section>
            <div class="table2">
                <table>
                    <tr>
                        <td style="white-space: nowrap"><h4>Teljes összeg: </h4></td>
                        <td style="white-space: nowrap" class="osszeg"><?php echo $total_price; ?> Ft</td>
                    </tr>
                    <tr>
                        <td style="white-space: nowrap"><h4>ÁFA: </h4></td>
                        <td style="white-space: nowrap" class="osszeg"><?php echo $afa; ?> Ft</td>
                    </tr>
                    <tr>
                        <td style="white-space: nowrap"><h4>Végösszeg: </h4></td>
                        <td style="white-space: nowrap" class="osszeg"><?php echo $vegso_osszeg; ?> Ft</td>
                    </tr>
                </table>
            </div>
        </section>
        <section class="szerkesztes">

            <form method="post">
                <input type="hidden" name="action" value="save_cart">
                <button type="submit" class="gomb">Tovább a fizetéshez</button>
            </form>
        </section>
    </section>
</main>

</body>

</html>