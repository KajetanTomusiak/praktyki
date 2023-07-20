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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS - Moje posty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css?family=Merriweather:400,900,900i" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="time.js"></script>
    <script src="tooltip.js"></script>
    <script src="button.js"></script>
</head>

<body class="bg-r">
    <a id="button"></a>

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

    <div class="container">
        <?php
        if (isset($_SESSION['id'])) {
            $db = new mysqli('localhost', 'root', '', 'cms');
            $sql = 'SELECT * FROM posts WHERE author = "'.$username.'" ORDER BY timestamp DESC';
            $result = $db->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $postID = $row['id'];
                    $title = $row['title'];
                    $img = $row['img'];
                    $author = $row['author'];
                    $timestamp = $row['timestamp'];
            ?>
        <div class="row my-5">
            <article class="col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3 p-5 bg-white post rounded">
                <small class="text-muted">
                    Dodał <?php echo $author; ?>,
                    <span data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $timestamp; ?>">
                        <script>
                        document.write(calculateTimeAgo('<?php echo $timestamp; ?>'));
                        </script>
                    </span>
                </small>
                <h2 class="display-6 mt-2 text-break"><?php echo $title; ?></h2>
                <img src="<?php echo $img; ?>" alt="post image" class="img-fluid">
                <div class="mt-3">
                    <a class="btn btn-primary" href="edit_post.php?id=<?php echo $postID; ?>" role="button">
                        <i class="fa fa-pencil me-2" aria-hidden="true"></i>Edytuj post
                    </a>
                    <a class="btn btn-danger" href="delete_post.php?id=<?php echo $postID; ?>" role="button">
                        <i class="fa fa-trash me-2" aria-hidden="true"></i>Usuń post
                    </a>
                </div>
            </article>
        </div>
        <?php
                }
            } else {
                echo '<div class="row my-5">
                <div class="col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-6 offset-lg-3 p-5 bg-white post rounded text-center">
                    <h3>Brak postów do wyświetlenia</h3>
                </div>
            </div>';
            }
            $db->close();
        } else {
            header("Location: login.php");
            exit();
        }
        ?>
    </div>

</body>

</html>