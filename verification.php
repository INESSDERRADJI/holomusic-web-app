<?php
session_start();

// Vérification des champs
if (!isset($_POST['email']) || empty($_POST['email']) ||
    !isset($_POST['password']) || empty($_POST['password'])) 
{
    header('location: connexion.php?message=You must fill in both fields');
    exit;
}

$email = trim($_POST['email']);
$email = htmlspecialchars($email);

$mdp = trim($_POST['password']);
$mdp = htmlspecialchars($mdp);

// Vérification du format email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('location: connexion.php?message=Invalid Email');
    exit;
}

include '../includes/connexion_bdd.php';

$q = 'SELECT idUser, username, password FROM users WHERE email = ?';
$req = $bdd->prepare($q);
$req->execute([$email]);
$result = $req->fetch();

if (empty($result) || !password_verify($mdp, $result['password'])) {
    writeLogLine(false, $email);
    header('location: connexion.php?message=Incorrect identifier');
    exit;
}

$_SESSION['email'] = $email;
$_SESSION['user_id'] = $result['idUser'];


writeLogLine(true, $email);

header('location: index.php');
exit;

function writeLogLine($success, $email)
{
    $log = fopen('log.txt', 'a+');
    $line = date('Y/m/d - H:i:s') . ' - Tentative de connexion ' 
          . ($success ? 'réussie : ' : 'échouée : ') 
          . $email . "\n";
    fputs($log, $line);
    fclose($log);
}

if (isset($_POST['email']) && !empty($_POST['email'])) {
    setcookie('email', $_POST['email'], time() + 24 * 3600);
}
