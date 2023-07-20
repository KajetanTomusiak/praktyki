<?php
session_start();
require_once "../db.php";

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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS - Dodawanie posta</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="../font-awesome-4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="../animation.js"></script>
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
                    <li class="nav-item me-2">
                        <a class="nav-link active" aria-current="page" href="../index.php">Strona główna</a>
                    </li>
                    <?php if (isset($username)) : ?>
                    <li class="nav-item me-2">
                        <a class="btn btn-primary" href="index.php" role="button">
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
                                <a class="dropdown-item" href="../my_posts.php">
                                    <i class="fa fa-pencil-square-o me-2" aria-hidden="true"></i>Moje posty
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="../logout.php">
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

    <?php
    // var_dump($_FILES);
        if (isset($_POST['postTitle']) && isset($_FILES['postImage']) && $_FILES['postImage']['error'] === 0) {
            $title = $_POST['postTitle'];
            $targetDir = 'img/';
        
            $filename = $_FILES['postImage']['name'];
            $fileNameArray = explode('.', $filename);
            $fileExtension = end($fileNameArray);
            $fileNameHash = md5($filename.time());
            $finalFileName = $targetDir.$fileNameHash.'.'.$fileExtension;
            move_uploaded_file($_FILES['postImage']['tmp_name'], '../'.$finalFileName);
        
            $author = $_POST['postAuthor'];
            $timestamp = date('Y-m-d H:i:s'); 
            $sql = "INSERT INTO posts (`id`, `title`, `img`, `author`, `timestamp`) VALUES (NULL, '$title', '$finalFileName', '$author', '$timestamp')";
            $db = new mysqli('localhost', 'root', '', 'cms');
            $result = $db->query($sql);
            echo '<div class="alert alert-success m-3 slide" role="alert">Post zapisany</div>';
        }
    ?>

    <div class="container bg-white">
        <div class="row my-5 py-5 mx-auto rounded">
            <main class="col-lg-8 offset-lg-2 py-5">
                <h1 class="display-4 text-center mb-5">Dodawanie posta</h1>
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
                            class="form-control <?php if(isset($_FILES['postImage']) && ($_FILES['postImage']['error'] !== 0 || empty($_FILES['postImage']['name']))) echo 'is-invalid'; ?>"
                            name="postImage" id="postImage">
                        <?php if(isset($_FILES['postImage']) && ($_FILES['postImage']['error'] !== 0 || empty($_FILES['postImage']['name']))): ?>
                        <div class="invalid-feedback">Pole Obrazek jest wymagane.</div>
                        <?php endif; ?>
                    </div>
                    <!-- <div class="mb-3">
                        <label for="postAuthor" class="form-label">Autor</label>
                        <input type="text"
                            class="form-control <?php if(isset($_POST['postAuthor']) && empty($_POST['postAuthor'])) echo 'is-invalid'; ?>"
                            name="postAuthor" id="postAuthor" maxlength="254" value="<?php echo $username ?>" readonly>
                        <?php if(isset($_POST['postAuthor']) && empty($_POST['postAuthor'])): ?>
                        <div class="invalid-feedback">Pole Autor jest wymagane.</div>
                        <?php endif; ?>
                    </div> -->
                    <button type="submit" class="btn btn-primary mt-2">Wyślij</button>
                </form>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>