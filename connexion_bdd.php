<?php
try {
    $bdd = new PDO(
        'mysql:host=127.0.0.1;port=8889;dbname=holomusic;charset=utf8',
        'root',
        'root'   
    );

    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}
?>
