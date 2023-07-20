<?php
session_start();
require_once "header.php";
require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $_SESSION['registration_error'] = "Proszę wypełnić wszystkie pola.";
    } else {
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = $dbcon->query($query);

        if ($result->num_rows == 0) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertQuery = "INSERT INTO users (username, password) VALUES ('$username', '$hashedPassword')";
            $dbcon->query($insertQuery);

            $_SESSION['registration_success'] = "Rejestracja zakończona pomyślnie. Możesz się teraz zalogować.";
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['registration_error'] = "Taki użytkownik już istnieje.";
        }
    }

    header("Location: register.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS - Rejestracja</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="container-fluid d-flex justify-content-center align-items-center vh-100 bg-r">
        <div class="row p-5 bg-light rounded">
            <form action="register.php" method="POST">
                <?php if (isset($_SESSION['registration_error'])) : ?>
                <div class="alert text-center alert-danger mb-3">
                    <?php echo $_SESSION['registration_error']; ?>
                </div>
                <?php unset($_SESSION['registration_error']); ?>
                <?php endif; ?>
                <p class="text-center display-4 pb-5">Rejestracja</p>
                <div class="form-group">
                    <input
                        class="form-control form-control-lg <?php if (isset($_SESSION['registration_error'])) echo 'is-invalid'; ?>"
                        type="text" name="username" placeholder="Nazwa użytkownika">
                    <?php if (isset($_SESSION['registration_error']) && empty($username)) : ?>
                    <div class="invalid-feedback"><?php echo $_SESSION['registration_error']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <input class="form-control form-control-lg" type="password" name="password" placeholder="Hasło"
                            id="password">
                        <button
                            style="border: 1px solid #ced4da; color: #6c757d !important; background-color: transparent !important; box-shadow: none !important; border-top-left-radius: 0 !important; border-bottom-left-radius: 0 !important; border-left-width: 0px !important"
                            class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <input class="btn btn-success btn-block" type="submit" name="register" value="Zarejestruj"><br>
                </div>
                <a class="d-flex justify-content-center text-center text-decoration-none" href="login.php">Logowanie</a>
            </form>
        </div>
    </div>
    </form>
    <script src="showPassword.js"></script>
</body>

</html>