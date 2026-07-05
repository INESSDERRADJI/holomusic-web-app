<?php
session_start();
$link = 'style_auteur.css';
$link2 = '';
$titre = 'Profil Auteur';
include '../Includes/header_index.php';
include '../Includes/connexion_bdd.php';

$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($userId <= 0) {
    echo '<p class="text-danger text-center mt-5">User invalid</p>';
    exit;
}

$qUser = "SELECT username, name, firstname, bio, image FROM users WHERE idUser = ?";
$stmtUser = $bdd->prepare($qUser);
$stmtUser->execute([$userId]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo '<p class="text-danger text-center mt-5">User not found</p>';
    include '../Includes/footer.php';
    exit;
}

$bio = trim((string)($user['bio'] ?? ''));
if ($bio === '') {
    $bio = 'Hi, I am a super cool author!';
}

$qArticles = "SELECT idArticle, title, date_of_pub, image
              FROM article
              WHERE User_id = :id
              ORDER BY date_of_pub DESC
              LIMIT 6";
$stmtArticles = $bdd->prepare($qArticles);
$stmtArticles->execute(['id' => $userId]);
$articles = $stmtArticles->fetchAll(PDO::FETCH_ASSOC);

$avatarSrc = !empty($user['image'])
    ? '../uploads/' . htmlspecialchars($user['image'], ENT_QUOTES, 'UTF-8')
    : '../uploads/photo_profil_default3.jpg';

$username = htmlspecialchars((string)$user['username'], ENT_QUOTES, 'UTF-8');
$fullname = htmlspecialchars(trim(($user['name'] ?? '') . ' ' . ($user['firstname'] ?? '')), ENT_QUOTES, 'UTF-8');
?>
<main class="author-page">
  <div class="container py-5">
    <div class="profil-card">
      <div class="row g-0 align-items-stretch">

        <div class="col-lg-5 profile-side">
          <div class="profile-inner">
            <img src="<?= $avatarSrc ?>" alt="Photo Profil" class="profil-photo">

            <h5 class="profile-username"><?= $username ?></h5>
            <p class="profile-fullname"><?= $fullname ?></p>

            <h6 class="profile-section">About</h6>
            <p class="profile-bio"><?= nl2br(htmlspecialchars($bio, ENT_QUOTES, 'UTF-8')) ?></p>
          </div>
        </div>

        <div class="col-lg-7 profile-articles">
          <h4 class="articles-title text-center">Last Articles</h4>

          <?php if (!empty($articles)): ?>
            <div class="row g-4">
              <?php foreach ($articles as $article): ?>
                <?php
                  $articleId = (int)$article['idArticle'];
                  $articleHref = "read_article_more.php?id={$articleId}";
                  $img = !empty($article['image'])
                      ? '../uploads/' . htmlspecialchars($article['image'], ENT_QUOTES, 'UTF-8')
                      : '../uploads/default_article.jpg';
                  $title = htmlspecialchars((string)$article['title'], ENT_QUOTES, 'UTF-8');
                  $date = !empty($article['date_of_pub']) ? date('d M Y', strtotime($article['date_of_pub'])) : '';
                ?>
                <div class="col-12 col-sm-6 col-lg-4">
                  <a class="article-link" href="<?= $articleHref ?>">
                    <div class="article-card">
                      <img src="<?= $img ?>" alt="<?= $title ?>" class="article-img">
                      <div class="article-body">
                        <p class="article-date"><?= htmlspecialchars($date, ENT_QUOTES, 'UTF-8') ?></p>
                        <h6 class="article-title"><?= $title ?></h6>
                      </div>
                    </div>
                  </a>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="no-articles text-center">No articles yet.</p>
          <?php endif; ?>

        </div>

      </div>
    </div>
  </div>
</main>

<?php include '../Includes/footer.php'; ?>
