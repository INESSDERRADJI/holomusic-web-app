<?php
session_start();
include '../Includes/connexion_bdd.php';
$link = 'style_index.css';
$link2 = '';
$titre = 'Home';
$script = 'cookies.js';
include '../Includes/header_index.php';

//on recupere les catégories
$reqCat = $bdd->query("SELECT * FROM category");
$categories = $reqCat->fetchAll(PDO::FETCH_ASSOC);
//on récupére les 6 derniers articles
$reqArticles = $bdd->query("
    SELECT * FROM article 
    ORDER BY date_of_pub DESC 
    LIMIT 6
");
$articles = $reqArticles->fetchAll(PDO::FETCH_ASSOC);

// AUTEURS les plus actifs 
$stmtAuthors = $bdd->query("
  SELECT u.idUser, u.name, u.firstname, u.image, u.bio,
         COUNT(a.idArticle) AS nb_articles
  FROM users u
  INNER JOIN article a ON a.User_id = u.idUser
  GROUP BY u.idUser, u.name, u.firstname, u.image, u.bio
  ORDER BY nb_articles DESC, u.idUser DESC
  LIMIT 12
");
$authors = $stmtAuthors->fetchAll(PDO::FETCH_ASSOC);
$slides = array_chunk($authors, 3);
?>

    <?php
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
                           
<main>
    <div id="popup-container">
        <h3>Privacy Notice</h3>
        <p>We use cookies to improve your experience on our website.</p>
        <p>By continuing to use this site, you agree to our use of cookies.</p>
        <button onclick="acceptCookies()">Accept</button>
        <button onclick="hidePopup()">Decline</button>
    </div>

    <div class="hero-carousel">
        <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="../Asset/chanteur1.webp" class="d-block w-100" alt="carousel1">
                </div>
                <div class="carousel-item">
                    <img src="../Asset/concert2.webp" class="d-block w-100" alt="carousel2">
                </div>
                <div class="carousel-item">
                    <img src="../Asset/chanteur.webp" class="d-block w-100" alt="carousel3">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>
    
    <section class="category-section">
      <div class="container text-center">
        <h3 class="category-title">Our HoloMusic Categories</h3>
        <div class="d-flex justify-content-center flex-wrap gap-3 category-wrapper">
          <?php foreach($categories as $cat): ?>
            <a href="../Front-Office/read_article.php?cat=<?= urlencode($cat['name']) ?>"
              class="btn category-btn">
              <?= htmlspecialchars($cat['name']) ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>



    <div class="articles-section container mt-5">
        <div class="row justify-content-center">
            <?php foreach($articles as $a): ?>
                <div class="col-md-4 mb-4">
                    <div class="article-card">
                        <img src="../uploads/<?= $a['image'] ?>" 
                            class="article-img" alt="<?= htmlspecialchars($a['title']) ?>">
                            <div class="article-body">
                                <small class="article-date">
                                    <?= strtolower(date("F Y", strtotime($a['date_of_pub']))) ?>
                                </small>
                                <h5 class="article-title">
                                    <?= htmlspecialchars($a['title']) ?>
                                </h5>
                                <p class="article-excerpt">
                                    <?= cleanExcerpt($a['body'], 200); ?>
                                </p>
                                <a href="../Front-Office/read_article_more.php?id=<?= $a['idArticle'] ?>" class="btn read-more-btn"> Read more </a>
                            </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <p class="articles-text text-center mt-4">
            <strong>
                Discover exclusive stories, deep insights, and the latest news from the music world.<br>
                Explore our full collection of articles and dive deeper into the artists you love.
            </strong>
        </p>

        <div class="text-center mt-3">
            <a href="../Front-Office/read_article.php" class="btn see-more-btn">
                See More
            </a>
        </div>
    </div>

<?php if (!empty($slides)): ?>
<section class="my-5">
  <div class="container">
    <div class="p-4 rounded-4 authors-section" style="background:#121317;">

      <h4 class="text-center text-white mb-4 authors-title">Meet our authors</h4>

      <div id="authorsCarousel" class="carousel slide text-center" data-bs-ride="carousel">

        <div class="d-flex justify-content-center mb-4 gap-2">
          <button class="carousel-control-prev position-relative" type="button"
                  data-bs-target="#authorsCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true" style="filter: invert(1);"></span>
            <span class="visually-hidden">Previous</span>
          </button>

          <button class="carousel-control-next position-relative" type="button"
                  data-bs-target="#authorsCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true" style="filter: invert(1);"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>

        <div class="carousel-inner py-2">

          <?php foreach ($slides as $sIndex => $group): ?>
            <div class="carousel-item <?= $sIndex === 0 ? 'active' : '' ?>">
              <div class="container">
                <div class="row g-4">

                  <?php foreach ($group as $i => $u): ?>
                    <?php
                      $userId = (int)($u['idUser'] ?? 0);

                      $fullName = trim(($u['firstname'] ?? '') . ' ' . ($u['name'] ?? ''));
                      if ($fullName === '') $fullName = 'Unknown';

                      $bio = $u['bio'] ?? '';

                      // Image safe + check file exists (évite l’affichage bizarre si le fichier manque)
                      $img = trim($u['image'] ?? '');
                      $imgSafe = $img !== '' ? basename($img) : '';
                      $imgFilePath = $imgSafe !== '' ? (__DIR__ . "/../uploads/" . $imgSafe) : '';
                      $hasImage = ($imgSafe !== '' && is_file($imgFilePath));
                      $imgSrc = "../uploads/" . htmlspecialchars($imgSafe, ENT_QUOTES, 'UTF-8');

                      // 3 visibles (desktop), 1 visible (mobile)
                      $colClass = ($i === 0) ? "col-lg-4" : "col-lg-4 d-none d-lg-block";
                    ?>

                    <div class="<?= $colClass ?>">
                      <div class="card h-100 border-0 text-center author-card" style="background:#121317;">
                        <div class="card-body p-4 d-flex flex-column align-items-center">

                          <a href="profil_auteur.php?id=<?= $userId ?>" class="text-decoration-none">
                            <div class="rounded-circle overflow-hidden shadow mb-3 d-flex align-items-center justify-content-center author-avatar"
                                 style="width:150px;height:150px;background:rgba(255,255,255,.08);">

                              <?php if ($hasImage): ?>
                                <img src="<?= $imgSrc ?>"
                                     alt="<?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?>"
                                     class="w-100 h-100"
                                     style="object-fit:cover; display:block;">
                              <?php else: ?>
                                <i class="bi bi-person-circle" style="font-size:64px;color:rgba(255,255,255,.85);"></i>
                              <?php endif; ?>

                            </div>
                          </a>

                          <h5 class="mb-1 text-white author-name">
                            <?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?>
                          </h5>

                          <p class="mb-3 text-white-50 small author-role">Author</p>

                          <!-- Bio clamp 3 lignes (CSS) -->
                          <div class="text-white-50 author-bio" style="width:100%;">
                            <span class="author-quote">&ldquo;</span>
                            <?= htmlspecialchars($bio, ENT_QUOTES, 'UTF-8') ?>
                          </div>

                        </div>
                      </div>
                    </div>

                  <?php endforeach; ?>

                </div>
              </div>
            </div>
          <?php endforeach; ?>

        </div>
      </div>

    </div>
  </div>
</section>


<?php endif; ?>

    <div class="newsletter-section">
        <div class="newsletter-content">
            <p>
                Stay in the loop! <br>
                Create your account and get access to the latest and most exclusive content,<br>
                <span class="highlight">fresh updates</span> and 
                <span class="highlight">hot news</span> straight from the music world.
            </p>
            <a href="../Front-Office/inscription.php" class="subscribe-btn">
                Subscribe
            </a>
        </div>
    </div>
</main>

<?php include '../Includes/footer.php' ?>