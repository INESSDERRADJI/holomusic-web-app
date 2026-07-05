<?php
session_start();
include '../Includes/connexion_bdd.php';
$titre='Articles';
$link='Style_read_article.css';
$link2='';
$script='search.js';
include '../Includes/header_index.php';

//la requete pour récuperer toutes les catégories
$reqCat = $bdd->query("SELECT * FROM category ORDER BY name ASC");
$categories = $reqCat->fetchAll(PDO::FETCH_ASSOC);

//On récupére les articles aussi
$reqArt = $bdd->query("SELECT * FROM article ORDER BY date_of_pub DESC");
$articles = $reqArt->fetchAll(PDO::FETCH_ASSOC);

//on organise les articles par catégories dans un tableau 
$articlesByCategory = [];

foreach ($articles as $a) {
    $catId = $a['Category_id'];
    $articlesByCategory[$catId][] = $a;
}

//meme fonction que dans index.php
function cleanExcerpt(string $html, int $maxLength = 200): string
    {
    $text = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = strip_tags($text);
    $text = str_replace(["\xC2\xA0", "\xA0", "\u{200B}", "\u{FEFF}"], ' ', $text);
    $text = trim(preg_replace('/\s+/u', ' ', $text));
    if ($text === '') {
        return 'No excerpt available.';
    }
    if (mb_strlen($text, 'UTF-8') > $maxLength) {
        $text = mb_substr($text, 0, $maxLength, 'UTF-8') . '...';
    }

    return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

?>

<main class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-5 page-top">
         <h1 class="text-white page-heading">
            Drive into the musical univers :  Discover  our <br><span class="highlight">inspiring</span> articles !
        </h1>

        <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
        <a href="article_post.php" class="btn-add-article">Add an article</a>
        <?php else: ?>
        <a href="read_article.php?post_error=login" class="btn-add-article">Add an article</a>
        <?php endif; ?>

    </div>

    <?php if (isset($_GET['post_error']) && $_GET['post_error'] === 'login'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            You must log in to your account in order to post your article!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

    <!--Barre de recherche en ajax -->
   <div class="container d-flex justify-content-center mt-4">
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Enter your search..." autocomplete="off">
            <span class="search-icon"><i class="bi bi-search"></i></span>
        </div>
    </div>
    <div id="searchResults" class="row mt-4"></div>


    <!-- Les articles trié par catégorie -->
    <?php foreach ($categories as $cat): ?>

        <section class="my-5">

            <h2 class="category-title"><?= htmlspecialchars($cat['name']) ?></h2>

            <div class="row mt-4">

            <?php 
            $catId = $cat['idCategory'];

            // ici si on a aucun article 
            if (!isset($articlesByCategory[$catId])): ?>
                 <p class="no-articles">No articles yet for this category.</p>

            <?php else: ?>

                <?php foreach ($articlesByCategory[$catId] as $a): ?>

                    <div class="col-md-4 mb-4">
                        <div class="article-card">

                            <img src="../uploads/<?= $a['image'] ?>" 
                                 class="article-img" 
                                 alt="<?= htmlspecialchars($a['title']) ?>">

                            <div class="article-body">

                                <small class="article-date">
                                    <?= strtolower(date("F Y", strtotime($a['date_of_pub']))) ?>
                                </small>

                                <h5 class="article-title">
                                    <?= htmlspecialchars($a['title']) ?>
                                </h5>

                                <p class="article-excerpt">
                                    <?= cleanExcerpt($a['body'], 200) ?>
                                </p>

                                <a href="../Front-Office/read_article_more.php?id=<?= $a['idArticle'] ?>" 
                                   class="btn read-more-btn">
                                    Read more
                                </a>

                            </div>

                        </div>
                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

            </div>

        </section>

    <?php endforeach; ?>

</main>

<?php include '../Includes/footer.php'; ?>

