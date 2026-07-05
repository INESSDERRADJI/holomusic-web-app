
<?php

// commentaires
$commentError = "";
$commentText  = "";

// POST : ajout commentaire / réponse
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_text'])) {

    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        header("Location: connexion.php?error=Please log in to comment.");
        exit;
    }

    $userId = (int)$_SESSION['user_id'];
    $commentText = trim($_POST['comment_text'] ?? '');
    $parentId = ($_POST['parent_id'] ?? '') !== '' ? (int)$_POST['parent_id'] : null;

    if ($commentText === '' || mb_strlen($commentText, 'UTF-8') > 1000) {
        $commentError = "Comment is empty or too long.";
    }

    if (empty($commentError) && $parentId !== null) {
        $check = $bdd->prepare("SELECT idComment FROM `comment` WHERE idComment = ? AND Article_id = ?");
        $check->execute([$parentId, $id]);
        if ($check->rowCount() === 0) {
            $commentError = "Invalid reply target.";
        }
    }

    if (empty($commentError)) {
        $ins = $bdd->prepare("
            INSERT INTO `comment` (`text`, `date`, `User_id`, `Article_id`, `parent_id`)
            VALUES (?, NOW(), ?, ?, ?)
        ");
        $ins->execute([$commentText, $userId, $id, $parentId]);

        header("Location: read_article_more.php?id={$id}#comments");
        exit;
    }
}

// GET : récupérer les commentaires 
$reqCom = $bdd->prepare("
    SELECT c.idComment, c.`text`, c.`date`, c.parent_id,
           u.firstname, u.name, u.image
    FROM `comment` c
    LEFT JOIN users u ON c.User_id = u.idUser
    WHERE c.Article_id = ?
    ORDER BY c.`date` ASC
");
$reqCom->execute([$id]);
$allComments = $reqCom->fetchAll(PDO::FETCH_ASSOC);

// construire l'arbre (simple)
$byId = [];
foreach ($allComments as $c) {
    $c['children'] = [];
    $byId[(int)$c['idComment']] = $c;
}

// attacher les enfants (on stocke des IDs d'enfants)
foreach ($byId as $cid => $c) {
    if (!empty($c['parent_id'])) {
        $pid = (int)$c['parent_id'];
        if (isset($byId[$pid])) {
            $byId[$pid]['children'][] = $cid;
        }
    }
}

// racines = commentaires sans parent (ou parent manquant)
$tree = [];
foreach ($byId as $cid => $c) {
    $pid = !empty($c['parent_id']) ? (int)$c['parent_id'] : 0;
    if ($pid === 0 || !isset($byId[$pid])) {
        $tree[] = $cid;
    }
}

?>