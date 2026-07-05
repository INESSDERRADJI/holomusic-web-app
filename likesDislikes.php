<?php
$liked = false;
$disliked = false;
$likeCount = 0;
$dislikeCount = 0;

$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;


$vote = $_GET['vote'] ?? null;
if ($vote && in_array($vote, ['like', 'dislike'], true)) {

    if ($userId <= 0) {
        header("Location: read_article_more.php?id={$id}&vote_error=login#actions");
        exit;
    }

    if ($vote === 'like') {
        // toggle like
        $q = $bdd->prepare("SELECT 1 FROM user_like_article WHERE User_id=? AND Article_id=?");
        $q->execute([$userId, $id]);

        if ($q->fetchColumn()) {
            $del = $bdd->prepare("DELETE FROM user_like_article WHERE User_id=? AND Article_id=?");
            $del->execute([$userId, $id]);
        } else {
           
            $bdd->prepare("DELETE FROM user_dislike_article WHERE User_id=? AND Article_id=?")->execute([$userId, $id]);
            $ins = $bdd->prepare("INSERT INTO user_like_article (User_id, Article_id) VALUES (?, ?)");
            $ins->execute([$userId, $id]);
        }

    } else { 
        $q = $bdd->prepare("SELECT 1 FROM user_dislike_article WHERE User_id=? AND Article_id=?");
        $q->execute([$userId, $id]);

        if ($q->fetchColumn()) {
            $del = $bdd->prepare("DELETE FROM user_dislike_article WHERE User_id=? AND Article_id=?");
            $del->execute([$userId, $id]);
        } else {
            
            $bdd->prepare("DELETE FROM user_like_article WHERE User_id=? AND Article_id=?")->execute([$userId, $id]);
            $ins = $bdd->prepare("INSERT INTO user_dislike_article (User_id, Article_id) VALUES (?, ?)");
            $ins->execute([$userId, $id]);
        }
    }

    header("Location: read_article_more.php?id={$id}#actions");
    exit;
}


if ($userId > 0) {
    $liked = (bool)$bdd->prepare("SELECT 1 FROM user_like_article WHERE User_id=? AND Article_id=?")
                      ->execute([$userId, $id]) ?: false;

    
    $st = $bdd->prepare("SELECT 1 FROM user_like_article WHERE User_id=? AND Article_id=?");
    $st->execute([$userId, $id]);
    $liked = (bool)$st->fetchColumn();

    $st = $bdd->prepare("SELECT 1 FROM user_dislike_article WHERE User_id=? AND Article_id=?");
    $st->execute([$userId, $id]);
    $disliked = (bool)$st->fetchColumn();
}

$st = $bdd->prepare("SELECT COUNT(*) FROM user_like_article WHERE Article_id=?");
$st->execute([$id]);
$likeCount = (int)$st->fetchColumn();

$st = $bdd->prepare("SELECT COUNT(*) FROM user_dislike_article WHERE Article_id=?");
$st->execute([$id]);
$dislikeCount = (int)$st->fetchColumn();

?>