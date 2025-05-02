<?php
session_start();

$is_logged_in = isset($_SESSION['username']);

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
$users = json_decode(file_get_contents('JSON/users.json'), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Az űrlapból beérkező adatok mentése a session-be
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['age'] = $_POST['age'];
    $_SESSION['discount'] = $_POST['discount'];
    $_SESSION['student_id'] = $_POST['student_id'];

    $users[$username]['email'] = $_POST['email'] ?? '';
    $users[$username]['age'] = $_POST['age'] ?? '';
    $users[$username]['discount'] = $_POST['discount'] ?? '';
    $users[$username]['student_id'] = $_POST['student_id'] ?? '';

    file_put_contents('JSON/users.json', json_encode($users, JSON_PRETTY_PRINT));

    // Visszairányítás a profil.php oldalra
    header("Location: profil.php");
    exit();
}

//Profil törlése
if (isset($_GET['delete'])) {
    // Törlés a users.json fájlból
    $users = json_decode(file_get_contents('JSON/users.json'), true);
    foreach ($users as $key => $user) {
        if ($user['username'] == $_SESSION['username']) {
            unset($users[$key]);
        }
    }
    file_put_contents('JSON/users.json', json_encode($users));

    // Kijelentkeztetés
    session_destroy();
    header('Location: login.php');
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
            <li>
                <a href="admin.php">Admin</a>
            </li>
        </ul>
    </nav>
</header>


<main>
    <section class="kep">
        <img src="img/userprofile.jpg" alt="profilkép">
    </section>
    <section class="info">
        <h3><?php echo $fullname; ?></h3>
        <form method="post" action="profil_edit.php">
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
                    <td><input type="email" name="email" value="<?php echo $email; ?>"</td>
                </tr>
                <tr>
                    <td>Életkor: </td>
                    <td><input type="number" name="age" value="<?php echo $age ?>"></td>
                </tr>
                <tr>
                    <td>Kedvezmények: </td>
                    <td>
                        <select name="discount">
                            <option value="Nincs kedvezmény" <?php if (!isset($_SESSION['discount']) || $_SESSION['discount'] == 'Nincs kedvezmény') echo 'selected'; ?>>Nincs kedvezmény</option>
                            <option value="Diák" <?php if (isset($_SESSION['discount']) && $_SESSION['discount'] == 'Diák') echo 'selected'; ?>>Diák kedvezmény</option>
                            <option value="Nyugdíjas" <?php if (isset($_SESSION['discount']) && $_SESSION['discount'] == 'Nyugdíjas') echo 'selected'; ?>>Nyugdíjas kedvezmény</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="white-space: nowrap">Diákigazolvány száma: </td>
                    <td><input type="text" name="student_id" value="<?php echo $student_id ?>"></td>
                </tr>
            </table>
            <section class="szerkesztes">
                <input type="submit" class="gomb" value="Mentés">
            </section>
        </form>
    </section>
</main>
</body>
</html>
