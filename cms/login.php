<?php
session_start();
require_once "header.php";
require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = "Proszę wypełnić wszystkie pola.";
    } else {
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = $dbcon->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password'];

            if (password_verify($password, $hashedPassword)) {
                $_SESSION['id'] = $row['id'];
                header("Location: index.php");
                exit();
            } else {
                $_SESSION['login_error'] = "Błędny login lub hasło.";
            }
        } else {
            $_SESSION['login_error'] = "Taki użytkownik nie istnieje.";
        }
    }

    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS - Logowanie</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container-fluid d-flex justify-content-center my-auto align-items-center vh-100 bg-r">
        <div class="row p-5 bg-light rounded">
            <form action="login.php" method="POST">
                <?php
                if (isset($_SESSION['login_error'])) {
                    echo '<div class="alert alert-danger text-center mb-3" role="alert">' . $_SESSION['login_error'] . '</div>';
                    unset($_SESSION['login_error']);
                }
                ?>
                <p class="text-center display-4 pb-5">Logowanie</p>
                <div class="form-group">
                    <input
                        class="form-control form-control-lg <?php if (isset($_SESSION['login_error'])) echo 'is-invalid'; ?>"
                        type="text" name="username" placeholder="Nazwa użytkownika">
                    <?php if (isset($_SESSION['login_error']) && empty($username)) : ?>
                    <div class="invalid-feedback">Proszę podać nazwę użytkownika.</div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <input
                            class="form-control form-control-lg <?php if (isset($_SESSION['login_error'])) echo 'is-invalid'; ?>"
                            type="password" name="password" placeholder="Hasło" id="password">
                        <?php if (isset($_SESSION['login_error']) && empty($password)) : ?>
                        <div class="invalid-feedback">Proszę podać hasło.</div>
                        <?php endif; ?>
                        <button
                            style="border: 1px solid #ced4da; color: #6c757d !important; background-color: transparent !important; box-shadow: none !important; border-top-left-radius: 0 !important; border-bottom-left-radius: 0 !important; border-left-width: 0px !important"
                            class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <input class="btn btn-success btn-block" type="submit" name="login" value="Zaloguj"><br>
                </div>

                <a class="d-flex justify-content-center text-center text-decoration-none" href="register.php">Rejestracja</a>
            </form>
        </div>
    </div>

    <script src="showPassword.js"></script>
</body>

</html>