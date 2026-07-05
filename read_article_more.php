<?php
session_start();
include '../Includes/connexion_bdd.php';

$titre = 'Read Article';
$link = 'Style_read_article_more.css';
$link2 = '';
$script = 'reply.js';
include '../Includes/header_index.php';

// Vérifier l'id
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Article not found.');
}

//recuperer l'article par id
$id = (int) $_GET['id'];
$stmt = $bdd->prepare("
  SELECT a.*,
         c.name AS category_name,
         u.idUser AS author_id,
         u.firstname AS author_firstname,
         u.name AS author_name
  FROM article a
  INNER JOIN category c ON a.Category_id = c.idCategory
  LEFT JOIN users u ON a.User_id = u.idUser
  WHERE a.idArticle = ?
");
$stmt->execute([$id]);
if ($stmt->rowCount() === 0) {
    die('Article does not exist.');
}
$article = $stmt->fetch(PDO::FETCH_ASSOC);

//commentaires
include '../Includes/comment.php';

//likes et dislikes
include '../Includes/likesDislikes.php';

?>



<!-- HTML  -->
<div class="container my-5 text-white" style="max-width: 900px;">
<div class="article-card">

    <div class="article-header">
        <h1 class="article-title"><?= htmlspecialchars($article['title']) ?></h1>
          <p class="article-meta">
            <?= date('F Y', strtotime($article['date_of_pub'])) ?> |
            Category: <?= htmlspecialchars($article['category_name']) ?>
        </p>
    </div>

    <div class="article-layout">

    
        <div class="article-image">
            <img
                src="../uploads/<?= htmlspecialchars($article['image']) ?>"
                alt="<?= htmlspecialchars($article['title']) ?>"
            >
        </div>

        
        <div class="article-content">
            <?= $article['body'] ?>
        </div>

    </div>


    <!-- Actions -->
     <div id="actions" class="article-actions d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <?php
              $authorId = (int)($article['author_id'] ?? $article['idUser'] ?? 0);
              $authorHref = $authorId > 0 ? "profil_auteur.php?id={$authorId}" : "#";
              ?>
              <a href="<?= $authorHref ?>" class="author-info">
                <i class="bi bi-person-circle"></i>
                <span>
                  <?= htmlspecialchars(($article['author_firstname'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8') ?>
                  <?= htmlspecialchars(($article['author_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                </span>
              </a>

            <div class="d-flex gap-3">
                <a class="btn btn-outline-light action-btn <?= $liked ? 'is-active' : '' ?>"
                href="read_article_more.php?id=<?= (int)$id ?>&vote=like#actions">
                <i class="bi bi-hand-thumbs-up"></i>
                <span class="ms-1 small"><?= (int)$likeCount ?></span>
                </a>
                <a class="btn btn-outline-light action-btn <?= $disliked ? 'is-active' : '' ?>"
                href="read_article_more.php?id=<?= (int)$id ?>&vote=dislike#actions">
                <i class="bi bi-hand-thumbs-down"></i>
                <span class="ms-1 small"><?= (int)$dislikeCount ?></span>
                </a>

                <a href="article_pdf.php?id=<?= (int)$id ?>" class="pdf-link">
                  <i class="bi bi-file-earmark-pdf"></i>
                  <span>PDF</span>
                </a>
                

            </div>
        </div>
         <?php if (isset($_GET['vote_error']) && $_GET['vote_error'] === 'login'): ?>
            <div class="alert alert-danger mt-3 mb-0 py-2">
              You must be logged in to vote.
            </div>
            <?php endif; ?>
        
    </div>

</div>
</div>


<!-- commentaires-->
<section id="comments" class="comments-section">
  <div class="container my-5 py-4">
    <div class="row justify-content-center">
      <div class="col-12 col-md-11 col-lg-9 col-xl-8">

        <div class="card border-0 comments-card">
          <div class="card-body p-4 text-white">

            <h4 class="text-center mb-4 pb-2">Comments</h4>

            <?php if (!empty($commentError)): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($commentError, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <?php
            function renderCommentHtml(int $commentId, array $byId, int $level = 0) {
                $c = $byId[$commentId];

                $author = trim(($c['firstname'] ?? '') . ' ' . ($c['name'] ?? ''));
                if ($author === '') $author = 'Unknown';

                $avatar = !empty($c['image'])
                    ? '../uploads/' . htmlspecialchars($c['image'], ENT_QUOTES, 'UTF-8')
                    : '';

                $date = date('d/m/Y H:i', strtotime($c['date']));
                $text = nl2br(htmlspecialchars($c['text'], ENT_QUOTES, 'UTF-8'));

                $indent = $level > 0 ? 'ms-5 mt-4' : 'mb-4';
                ?>

                <div class="d-flex flex-start <?= $indent ?>">
                  <?php if ($avatar): ?>
                    <img class="rounded-circle shadow-1-strong me-3 comment-avatar"
                         src="<?= $avatar ?>" alt="avatar" width="65" height="65">
                  <?php else: ?>
                    <div class="rounded-circle shadow-1-strong me-3 comment-avatar d-flex align-items-center justify-content-center">
                      <i class="bi bi-person-circle"></i>
                    </div>
                  <?php endif; ?>

                  <div class="card w-100 border-0 comment-bubble">
                    <div class="card-body p-4">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h5 class="mb-0 text-white"><?= htmlspecialchars($author, ENT_QUOTES, 'UTF-8') ?></h5>
                          <p class="small text-white-50 mb-0"><?= htmlspecialchars($date, ENT_QUOTES, 'UTF-8') ?></p>
                        </div>

                        <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
                          <a href="#commentForm" class="comment-reply"
                             onclick='setReply(<?= (int)$c["idComment"] ?>, <?= json_encode($author) ?>)'>
                            <i class="bi bi-reply"></i> <span class="small">Reply</span>
                          </a>
                        <?php endif; ?>
                      </div>

                      <p class="mt-3 mb-0 text-white-75"><?= $text ?></p>

                      <?php if (!empty($c['children'])): ?>
                        <?php foreach ($c['children'] as $childId): ?>
                          <?php renderCommentHtml((int)$childId, $byId, $level + 1); ?>
                        <?php endforeach; ?>
                      <?php endif; ?>

                    </div>
                  </div>
                </div>

                <?php
            }
            ?>

            <?php if (empty($tree)): ?>
              <p class="text-center text-white-50 mb-4">No comments yet.</p>
            <?php else: ?>
              <?php foreach ($tree as $rootId): ?>
                <?php renderCommentHtml((int)$rootId, $byId, 0); ?>
              <?php endforeach; ?>
            <?php endif; ?>

            <hr class="my-4" style="border-color: rgba(255,255,255,.12);">

            <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
              <form id="commentForm" method="POST" action="read_article_more.php?id=<?= (int)$id ?>#comments">
                <input type="hidden" name="parent_id" id="parent_id" value="">

                <div id="replyInfo" class="small text-white-50 mb-2 d-none">
                  Replying to <span id="replyName"></span>
                  <button type="button" class="btn btn-sm btn-link text-white-50 p-0 ms-2" onclick="cancelReply()">Cancel</button>
                </div>

                <textarea name="comment_text" id="comment_text" class="form-control mb-2" rows="3"
                          placeholder="Write a comment..."><?= htmlspecialchars($commentText, ENT_QUOTES, 'UTF-8') ?></textarea>
                <button class="btn btn-danger">Post</button>
              </form>
            <?php else: ?>
              <div class="alert alert-info mb-0">Please log in to post a comment.</div>
            <?php endif; ?>

          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<?php include('../Includes/footer.php'); ?>
