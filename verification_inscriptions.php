<?php 
session_start();
include '../includes/connexion_bdd.php';

if (
    empty($_POST['email']) ||
    empty($_POST['mdp']) ||
    empty($_POST['username']) ||
    empty($_POST['name']) ||
    empty($_POST['firstname']) || 
    empty($_POST['date_of_birth']) ||
    empty($_POST['mdp-confirm'])
) {
    $_SESSION['error'] = 'You must fill all fields!';
    $_SESSION['old_data'] = $_POST;
    header('location: inscription.php');
    exit;
}

$email          = trim($_POST['email']);
$username       = trim($_POST['username']);
$name           = trim($_POST['name']);
$firstname      = trim($_POST['firstname']); 
$date_of_birth  = trim($_POST['date_of_birth']);
$mdp            = trim($_POST['mdp']);
$mdp_confirm    = trim($_POST['mdp-confirm']);
$defaultPhoto = 'photo_profil_default3.jpg';


if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
    $_SESSION['error'] = 'Username can only contain letters, numbers, _ and -';
    $_SESSION['old_data'] = $_POST;
    header('location: inscription.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || preg_match('/[\/\\\]/', $email)) {
    $_SESSION['error'] = 'Invalid Email';
    $_SESSION['old_data'] = $_POST;
    header('location: inscription.php');
    exit;
}

if (!preg_match("/^[a-zA-ZÀ-ÿ '-]+$/", $firstname)) {
    $_SESSION['error'] = 'Firstname contains invalid characters';
    $_SESSION['old_data'] = $_POST;
    header('location: inscription.php');
    exit;
}

if (!preg_match("/^[a-zA-ZÀ-ÿ '-]+$/", $name)) {
    $_SESSION['error'] = 'Name contains invalid characters';
    $_SESSION['old_data'] = $_POST;
    header('location: inscription.php');
    exit;
}

if (strlen($mdp) < 6 || strlen($mdp) > 20) {
    $_SESSION['error'] = 'The password must be between 6 and 20 characters!';
    $_SESSION['old_data'] = $_POST;
    header('location: inscription.php');
    exit;
}

if ($mdp !== $mdp_confirm) {
    $_SESSION['error'] = 'Passwords do not match!';
    $_SESSION['old_data'] = $_POST;
    header('location: inscription.php');
    exit;
}

$q = 'SELECT idUser FROM users WHERE email = ?';
$req = $bdd->prepare($q);
$req->execute([$email]);
if ($req->fetch()) {
    $_SESSION['error'] = 'Email already used!';
    $_SESSION['old_data'] = $_POST;
    header('location: inscription.php');
    exit;
}

$q = 'SELECT idUser FROM users WHERE username = ?';
$req = $bdd->prepare($q);
$req->execute([$username]);
if ($req->fetch()) {
    $_SESSION['error'] = 'Username already used!';
    $_SESSION['old_data'] = $_POST;
    header('location: inscription.php');
    exit;
}

$hashedPassword = password_hash($mdp, PASSWORD_DEFAULT);

$q = "INSERT INTO users (username, name, firstname, birthdate, email, password, image)
      VALUES (?, ?, ?, ?, ?, ?, ?)";
$req = $bdd->prepare($q);
$success = $req->execute([
    htmlspecialchars($username),
    htmlspecialchars($name),
    htmlspecialchars($firstname), 
    htmlspecialchars($date_of_birth),
    htmlspecialchars($email),
    $hashedPassword,
    $defaultPhoto
]);

if (!$success) {
    $_SESSION['error'] = 'Error while inserting data!';
    $_SESSION['old_data'] = $_POST;
    header('location: inscription.php');
    exit;
}

unset($_SESSION['old_data']);

$_SESSION['success'] = 'Account created successfully!';
header('location: connexion.php');
exit;
?>
