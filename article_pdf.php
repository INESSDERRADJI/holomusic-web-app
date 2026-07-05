<?php 
require __DIR__ . '/../Includes/dompdf/autoload.inc.php';
require __DIR__ . '/../Includes/connexion_bdd.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_GET['id']) || empty($_GET['id'])) {
  die("Article not found.");
}
$id = (int)$_GET['id'];
if ($id <= 0) die("ID invalide");

$stmt = $bdd->prepare("SELECT title, body, image FROM article WHERE idArticle = ?");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) die("Article does not exist.");

$title = $article['title'];
$body  = $article['body'];     
$imageName = $article['image'];


$imageUrl = '';
if (!empty($imageName)) {
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'];
  $projectPath = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/');
  $imageUrl = $scheme . '://' . $host . $projectPath . '/uploads/' . rawurlencode($imageName);
}


$html = '
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <style>
    @page { margin: 20mm; }
    body { font-family: Raleway, sans-serif; font-size: 12px; }
    h1 { font-size: 20px; margin: 0 0 12px; }
    img { max-width: 100%; height: auto; margin: 10px 0 15px; }
  </style>
</head>
<body>
  <h1>'.htmlspecialchars($title, ENT_QUOTES, "UTF-8").'</h1>
  '.(!empty($imageUrl) ? '<img src="'.htmlspecialchars($imageUrl, ENT_QUOTES, "UTF-8").'">' : '').'
  <div>'.$body.'</div>
</body>
</html>';

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();


$dompdf->stream("article-$id.pdf", ["Attachment" => true]);
exit;




?>