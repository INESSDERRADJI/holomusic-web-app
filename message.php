<?php
$allowedTypes = ['success','danger','warning','info'];

if (!empty($_GET['message']) && !empty($_GET['type']) && in_array($_GET['type'], $allowedTypes, true)) {
    echo '<div style="margin-top:100px; margin-left:50px; margin-right:50px;" class="alert alert-' .
         htmlspecialchars($_GET['type'], ENT_QUOTES, 'UTF-8') .
         ' alert-dismissible fade show" role="alert">' .
         htmlspecialchars($_GET['message'], ENT_QUOTES, 'UTF-8') .
         '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
}
?>
