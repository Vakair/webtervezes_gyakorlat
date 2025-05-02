<?php
// Ellenőrzi, hogy az űrlap elküldésre került-e
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ellenőrzi, hogy minden szükséges mezőt kitöltöttek-e
    if (!empty($_POST['username']) && !empty($_POST['fullname']) && !empty($_POST['password']) && !empty($_POST['confirm_password']) && isset($_POST['accept'])) {
        // Ellenőrzi, hogy a jelszavak megegyeznek-e
        if ($_POST['password'] === $_POST['confirm_password']) {
            if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $_POST['password'])) {
                // Ellenőrzi, hogy a felhasználónév foglalt-e már
                $existing_users = json_decode(file_get_contents("JSON/users.json"), true);
                if (!isset($existing_users[$_POST['username']])) {
                    // Hasheljük a jelszót
                    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

                    // Elkészíti a felhasználói adatokat egy tömbben
                    $user_data = array(
                        'username' => $_POST['username'],
                        'fullname' => $_POST['fullname'],
                        'password' => $hashed_password,
                        'email' => '',
                        'age' => '',
                        'discount' => '',
                        'student_id' => '',
                    );

                    // Konvertálja a tömböt JSON formátumba
                    $existing_users[$_POST['username']] = $user_data;
                    $json_data = json_encode($existing_users, JSON_PRETTY_PRINT);


                    // Tárolja el a JSON adatokat egy fájlban
                    file_put_contents('JSON/users.json', $json_data);

                    // Sikeres regisztráció üzenet
                    $success_message = 'Sikeres regisztráció!';
                } else {
                    // Foglalt felhasználónév üzenet
                    $error_message = 'A felhasználónév már foglalt. Kérem válasszon másikat!';
                }
            } else {
                // Jelszó nem megfelelő üzenet
                $error_message = 'A jelszónak legalább 8 karakter hosszúnak kell lennie, és tartalmaznia kell legalább egy kisbetűt, egy nagybetűt és egy számot is!';
            }
        } else {
            // Jelszó nem egyezik üzenet
            $error_message = 'A jelszavak nem egyeznek meg!';
        }
    } else {
        // Kitöltetlen mezők vagy feltételek elfogadása nélkül üzenet
        $error_message = 'Kérem töltse ki az összes mezőt és fogadja el az általános szerződési feltételeket!';
    }
}
?>
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
?>





<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Konda | Regisztráció</title>
    <link rel="stylesheet" href="css/css.css">
    <link rel="stylesheet" href="css/login_register.css">
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



<?php if(isset($error_message)): ?>
    <div class="error-message"><?php echo $error_message; ?></div>
<?php endif; ?>

<?php if(isset($success_message)): ?>
    <div class="success-message"><?php echo $success_message; ?></div>
<?php endif; ?>



<div class="wrapper">
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <h1>Regisztráció</h1>
        <div class="input-box">
            <label>
                <input type="text" placeholder="Teljesnév" name="fullname" required>
            </label>
        </div>

        <div class="input-box">
            <label>
                <input type="text" placeholder="Felhasználónév" name="username" required>
            </label>
        </div>

        <div class="input-box">
            <label>
                <input type="password" placeholder="Jelszó" name="password" required>
            </label>
        </div>

        <div class="input-box">
            <label>
                <input type="password" placeholder="Jelszó újra" name="confirm_password" required>
            </label>
        </div>

        <div class="remember-forgot">
            <label><input type="checkbox" name="accept">
                Elfogadom az általános szerződési feltételeket
            </label>
        </div>

        <button type="submit" class="btn">Regisztráció</button>



        <div class="login-link">
            <p>Már van fiókod?
                <a href="login.php">Bejelentkezés</a></p>
        </div>

    </form>
</div>

</body>
</html>
