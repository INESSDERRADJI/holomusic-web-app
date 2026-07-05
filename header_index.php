<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo $titre ?></title>
    <link rel="shortcut icon" href="../Asset/Logotab.ico">
        
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            crossorigin="anonymous"
            defer></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
            crossorigin="anonymous"
            defer></script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">

    <!--Du script js a injecter ci besoin -->
    <?php if (!empty($script)): ?>
    <script src="../JS/<?= htmlspecialchars($script, ENT_QUOTES, 'UTF-8') ?>" defer></script>
    <?php endif; ?>


    <!-- Script de la clé API pour TinyMCE -->
    <script src="../JS/vendor/tinymce/tinymce.min.js"></script>

    <!-- Global styles -->
    <link rel="stylesheet" href="../CSS/style_footer.css">
    <link rel="stylesheet" href="../CSS/header_style.css">


    <!-- Page-specific CSS -->
    <?php if (!empty($link)) : ?>
    <link rel="stylesheet" href="../CSS/<?= htmlspecialchars(basename($link), ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

    <?php if (!empty($link2)) : ?>
    <link rel="stylesheet" href="../CSS/<?= htmlspecialchars(basename($link2), ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

    
</head>

<body>
<header>
    <nav class="navbar navbar-expand-lg bg-dark" data-bs-theme="dark">
        <div class="container-fluid text-center">

            <!-- LOGO -->
            <a class="navbar-brand px-3 mb-2" href="../Front-Office/index.php">
                <img src="../Asset/Logo.svg" width="50px" height="50px" alt="logo">
            </a>

            <p class="fw-bold fs-3 mt-3 text-center">HOLOWMUSIC</p>

            <!-- Bouton BURGER -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>


            <!-- MENU -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <?php
                    echo '
                            <a class="nav-link px-3 ' . ($titre == "Acceuil" ? "active" : "") . '" 
                                href="../Front-Office/index.php">Home</a>
                              <a class="nav-link px-3  ' . ($titre == "Articles" ? "active" : "") . '" 
                            href="../Front-Office/read_article.php">Articles</a>
                            <a class="nav-link px-3 ' . ($titre == "Profil" ? "active" : "") . '" 
                                href="../Front-Office/profil.php">Profil</a>
                        <a class="btn btn-danger px-3 btn-header" ';

                    $isLogged = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
                    if (!$isLogged) {
                        echo 'href="../Front-Office/connexion.php">Login';
                    } else {
                        echo 'href="../Front-Office/deconnexion.php">Log out';
                    }

                    echo '</a>';
                    ?>
                </div>
            </div>

        </div>
    </nav>
</header>

