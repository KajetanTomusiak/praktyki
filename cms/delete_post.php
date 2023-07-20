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

// Usunięcie posta
$db = new mysqli('localhost', 'root', '', 'cms');
$sql = "DELETE FROM posts WHERE id = $postId";
$result = $db->query($sql);
if ($result) {
    header("Location: index.php");
    exit();
} else {
    echo "Błąd podczas usuwania posta.";
}
?>
