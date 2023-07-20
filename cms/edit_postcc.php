<?php
session_start();
require_once "db.php";

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
    $author = $_POST['postAuthor'];
    $timestamp = date('Y-m-d H:i:s'); 
    $postAuthor = $_POST['author'];
    $postTitle = $_POST['title'];
    $postImage = $_POST['img'];

    $db = new mysqli('localhost', 'root', '', 'cms');
    $sql = "UPDATE posts SET title = ?, author = ?, timestamp = ? WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("sssi", $title, $author, $timestamp, $postId);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Błąd podczas aktualizacji posta.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS - Edytuj post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="bg-r">
    <div class="container bg-white">
        <div class="row my-5 py-5 mx-auto rounded">
            <main class="col-lg-8 offset-lg-2 py-5">
                <h1 class="display-4 text-center mb-5">Edycja posta</h1>
                <form action="#" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="postTitle" class="form-label">Tytuł</label>
                        <input type="text"
                            class="form-control <?php if(isset($_POST['postTitle']) && empty($_POST['postTitle'])) echo 'is-invalid'; ?>"
                            name="postTitle" id="postTitle" maxlength="254" value="<?php echo $postTitle; ?>">
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
                    <div class="mb-3">
                        <label for="postAuthor" class="form-label">Autor</label>
                        <input type="text"
                            class="form-control <?php if(isset($_POST['postAuthor']) && empty($_POST['postAuthor'])) echo 'is-invalid'; ?>"
                            name="postAuthor" id="postAuthor" maxlength="254" value="<?php echo $postAuthor; ?>">
                        <?php if(isset($_POST['postAuthor']) && empty($_POST['postAuthor'])): ?>
                        <div class="invalid-feedback">Pole Autor jest wymagane.</div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                </form>
            </main>
        </div>
    </div>
</body>

</html>