<?php
session_start();
include '../Includes/connexion_bdd.php';

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
    
if (!isset($_GET["query"]) || empty(trim($_GET["query"]))) {
    echo "";
    exit;
}

$query = trim($_GET["query"]);
$query = htmlspecialchars($query, ENT_QUOTES);

// Requête SQL
$sql = $bdd->prepare("
    SELECT article.*, category.name AS category_name
    FROM article
    INNER JOIN category ON article.Category_id = category.idCategory
    WHERE article.title LIKE ? 
       OR article.body LIKE ?
    ORDER BY article.date_of_pub DESC
");
$sql->execute(["%$query%", "%$query%"]);
$results = $sql->fetchAll(PDO::FETCH_ASSOC);

if (count($results) === 0) {
    echo "<p style='color:white; padding:10px;'>No results for <strong>$query</strong>.</p>";
    exit;
}

foreach ($results as $a) {
?>
    <div class="col-md-4 mb-4">
    <div class="article-card">
        <img src="../uploads/<?= $a['image'] ?>" class="article-img">
        <div class="article-body">
            <small class="article-date"><?= strtolower(date("F Y", strtotime($a['date_of_pub']))) ?></small>
            <h5 class="article-title"><?= htmlspecialchars($a['title']) ?></h5>
            <p class="article-excerpt"><?= cleanExcerpt($a['body'], 200) ?></p>
            <a href="../Front-Office/read_article_more.php?id=<?= $a['idArticle'] ?>" class="btn read-more-btn">
                Read more
            </a>
        </div>
    </div>
</div>
<?php
}
?>

