<?php
session_start();

// Ellenőrzi, hogy az űrlap elküldésre került-e
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ellenőrzi, hogy minden szükséges mezőt kitöltöttek-e
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        // Ellenőrzi, hogy a felhasználónév létezik-e és helyes-e a jelszó
        $users = json_decode(file_get_contents('JSON/users.json'), true);
        if (isset($users[$_POST['username']])) {
            $user_data = $users[$_POST['username']];
            if (password_verify($_POST['password'], $user_data['password'])) {
                // Bejelentkezés sikeres, beállítja a felhasználói session-t
                $_SESSION['username'] = $_POST['username'];
                // Átirányítás a profil oldalra
                header("Location: profil.php");
                exit();
            } else {
                // Hibás jelszó üzenet
                $error_message = 'Hibás felhasználónév vagy jelszó!';
            }
        } else {
            // Hibás felhasználónév üzenet
            $error_message = 'Hibás felhasználónév vagy jelszó!';
        }
    } else {
        // Kitöltetlen mezők üzenet
        $error_message = 'Kérem töltse ki mindkét mezőt!';
    }
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
    <title>Konda | Bejelentkezés</title>
    <link rel="stylesheet" href="css/css.css">
    <link rel="stylesheet" href="css/login_register.css">
</head>
<body>
<header>
    <div class="logo">Konda</div>
    <nav class="nav-bar">
        <ul>

            <li><a href="index.php">Főoldal</a></li>
            <li><a href="service1.php">Szolgáltatások</a></li>
            <li><a href="cart.php">Kosár</a></li>
            <li><a href="profil.php">Profil</a></li>
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

<div class="wrapper">
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <h1>Bejelentkezés</h1>
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


        <button type="submit" class="btn">Bejelentkezés</button>

        <div class="login-link">
            <p>Még nincs fiókod?
                <a href="register.php">Regisztráció</a></p>
        </div>
    </form>
</div>

</body>
</html>
