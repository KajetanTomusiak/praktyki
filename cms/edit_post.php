<?php
session_start();
require_once "db.php";
require_once "header.php";

// Sprawdzanie czy użytkownik jest zalogowany
if (isset($_SESSION['id'])) {
    $db = new mysqli('localhost', 'root', '', 'cms');
    $sql = 'SELECT * FROM users WHERE id = ' . $_SESSION['id'];
    $result = $db->query($sql);
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $username = $user['username'];
    }
    $db->close();
} else {
    header("Location: login.php");
    exit();
}

// Sprawdzanie czy przekazano poprawne id posta
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $postId = $_GET['id'];
    $db = new mysqli('localhost', 'root', '', 'cms');
    $sql = 'SELECT * FROM posts WHERE id = ' . $postId;
    $result = $db->query($sql);
    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
        $postAuthor = $post['author'];

        // Sprawdzanie czy zalogowany użytkownik jest autorem posta
        if ($postAuthor != $username) {
            header("Location: index.php");
            exit();
        }
    } else {
        header("Location: index.php");
        exit();
    }
    $db->close();
} else {
    header("Location: index.php");
    exit();
}

// Aktualizacja posta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['postTitle'];
    // $author = $_POST['postAuthor'];
    $timestamp = date('Y-m-d H:i:s'); 

    $errors = [];

    if (empty($title)) {
        $errors[] = "Pole Tytuł jest wymagane.";
    }

    if (count($errors) === 0) {
        $db = new mysqli('localhost', 'root', '', 'cms');
        $sql = "UPDATE posts SET title = ?, author = ?, timestamp = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sssi", $title, $author, $timestamp, $postId);

        if ($stmt->execute()) {
            $_SESSION['postUpdated'] = true;
            header("Location: index.php");
            exit();
        } else {
            $errorMessage = "Błąd podczas aktualizacji posta.";
        }
    }
}

// Usuwanie posta
if (isset($_POST['deletePost'])) {
    $db = new mysqli('localhost', 'root', '', 'cms');
    $sql = "DELETE FROM posts WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $postId);

    if ($stmt->execute()) {
        $_SESSION['postDeleted'] = true;
        header("Location: index.php");
        exit();
    } else {
        $errorMessage = "Błąd podczas usuwania posta.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS - Edycja posta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="bg-r">

<nav class="navbar navbar-expand-md navbar-light bg-white">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="#">CMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item me-3">
                        <a class="nav-link active" aria-current="page" href="index.php">Strona główna</a>
                    </li>
                    <?php if (isset($username)) : ?>
                    <li class="nav-item me-3">
                        <a class="btn btn-primary" href="admin/index.php" role="button">
                            <i class="fa fa-plus me-2" aria-hidden="true"></i>Dodaj post
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo $username; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item" href="my_posts.php">
                                    <i class="fa fa-pencil-square-o me-2" aria-hidden="true"></i>Moje posty
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="logout.php">
                                    <i class="fa fa-sign-out me-2" aria-hidden="true"></i>Wyloguj się
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container bg-white">
        <div class="row my-5 py-5 mx-auto rounded">
            <main class="col-lg-8 offset-lg-2 py-5">
                <h1 class="display-4 text-center mb-5">Edycja posta</h1>
                <?php if(isset($_SESSION['postUpdated'])): ?>
                <div class="alert alert-success" role="alert">
                    Post został zaktualizowany.
                </div>
                <?php unset($_SESSION['postUpdated']); ?>
                <?php endif; ?>
                <?php if(isset($_SESSION['postDeleted'])): ?>
                <div class="alert alert-success" role="alert">
                    Post został usunięty.
                </div>
                <?php unset($_SESSION['postDeleted']); ?>
                <?php endif; ?>
                <?php if(isset($errorMessage)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $errorMessage; ?>
                </div>
                <?php endif; ?>
                <form action="#" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="postTitle" class="form-label">Tytuł</label>
                        <input type="text"
                            class="form-control <?php if(isset($_POST['postTitle']) && empty($_POST['postTitle'])) echo 'is-invalid'; ?>"
                            name="postTitle" id="postTitle" maxlength="254"
                            value="<?php if(isset($_POST['postTitle'])) echo $_POST['postTitle']; ?>">
                        <?php if(isset($_POST['postTitle']) && empty($_POST['postTitle'])): ?>
                        <div class="invalid-feedback">Pole Tytuł jest wymagane.</div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="postImage" class="form-label">Obrazek</label>
                        <input type="file"
                            class="form-control <?php if(isset($_FILES['postImage']) && ($_FILES['postImage']['error'] !== 0 || empty($_FILES['postImage']['name']))) echo 'is-invalid'; ?> "
                            name="postImage" id="postImage">
                        <?php if(isset($_FILES['postImage']) && ($_FILES['postImage']['error'] !== 0 || empty($_FILES['postImage']['name']))): ?>
                        <div class="invalid-feedback">Pole Obrazek jest wymagane.</div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Zapisz zmiany</button>
                </form>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
