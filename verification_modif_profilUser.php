<?php
session_start();

include '../Includes/connexion_check.php';
include '../Includes/connexion_bdd.php';


if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    header('Location: connexion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   
    $rawPassword    = isset($_POST['password'])      ? $_POST['password']      : '';
    $rawOldPassword = isset($_POST['old_password'])  ? $_POST['old_password']  : '';

    $username   = trim($_POST['username'] ?? '');
    $firstname  = trim($_POST['firstname'] ?? '');
    $name       = trim($_POST['name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = trim($rawPassword);
    $oldPassword= trim($rawOldPassword);
    $bio        = trim($_POST['bio'] ?? '');

   
    if (empty($username)) {
        header('Location: profil.php?message=Username is required&type=danger');
        exit;
    }
    if (empty($firstname)) {
        header('Location: profil.php?message=Firstname is required&type=danger');
        exit;
    }
    if (empty($name)) {
        header('Location: profil.php?message=Name is required&type=danger');
        exit;
    }
    if (empty($email)) {
        header('Location: profil.php?message=Email is required&type=danger');
        exit;
    }

    // Validation du format 
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
        header('Location: profil.php?message=Username can only contain letters, numbers, _ and -&type=danger');
        exit;
    }

    if (!preg_match("/^[a-zA-ZÀ-ÿ '-]+$/", $firstname)) {
        header('Location: profil.php?message=Firstname contains invalid characters&type=danger');
        exit;
    }

    if (!preg_match("/^[a-zA-ZÀ-ÿ '-]+$/", $name)) {
        header('Location: profil.php?message=Name contains invalid characters&type=danger');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: profil.php?message=Invalid email address&type=danger');
        exit;
    }

    if (preg_match('/[\/\\\]/', $email)) {
        header('Location: profil.php?message=Email contains invalid characters&type=danger');
        exit;
    }

    if (($rawPassword !== '' && $password === '') || ($rawOldPassword !== '' && $oldPassword === '')) {
        header('Location: profil.php?message=Password fields cannot contain only spaces&type=danger');
        exit;
    }


    
    $q = 'SELECT idUser FROM users WHERE username = ? AND idUser != ?';
    $req = $bdd->prepare($q);
    $req->execute([$username, $_SESSION['user_id']]);
    if ($req->fetch()) {
        header('Location: profil.php?message=Username already in use&type=danger');
        exit;
    }

    
    $q = 'SELECT idUser FROM users WHERE email = ? AND idUser != ?';
    $req = $bdd->prepare($q);
    $req->execute([$email, $_SESSION['user_id']]);
    if ($req->fetch()) {
        header('Location: profil.php?message=Email already in use&type=danger');
        exit;
    }

    
    $q = 'SELECT password FROM users WHERE idUser = ?';
    $req = $bdd->prepare($q);
    $req->execute([$_SESSION['user_id']]);
    $user = $req->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Location: profil.php?message=User not found&type=danger');
        exit;
    }

    $hashedPassword = null;

   
    if (!empty($oldPassword) && empty($password)) {
        header('Location: profil.php?message=Please enter a new password&type=danger');
        exit;
    }

    if (!empty($password) && empty($oldPassword)) {
        header('Location: profil.php?message=Please enter your old password&type=danger');
        exit;
    }

    
    if (!empty($password)) {

        if (strlen($password) < 6 || strlen($password) > 20) {
            header('Location: profil.php?message=Password must be between 6 and 20 characters&type=danger');
            exit;
        }

        
        if (empty($oldPassword) || !password_verify($oldPassword, $user['password'])) {
            header('Location: profil.php?message=Old password is incorrect&type=danger');
            exit;
        }

        
        if (password_verify($password, $user['password'])) {
            header('Location: profil.php?message=New password must be different from old password&type=danger');
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    }

    
    $imageName = null;

    if (!empty($_FILES['image']['name'])) {

    $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];

    $tmp = $_FILES['image']['tmp_name'];
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExt)) {
        header('Location: profil.php?message=Invalid image format&type=danger');
        exit;
    }

    $imageName  = 'photo_' . time() . '.jpg';
    $uploadPath = '../uploads/' . $imageName;

    [$w, $h] = getimagesize($tmp);

    $createFunc = match ($ext) {
        'jpg', 'jpeg' => 'imagecreatefromjpeg',
        'png'         => 'imagecreatefrompng',
        'gif'         => 'imagecreatefromgif',
    };

    $src = $createFunc($tmp);
    $dst = imagecreatetruecolor(300, 300);

    if (in_array($ext, ['png', 'gif'])) {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
    }

    imagecopyresampled($dst, $src, 0, 0, 0, 0, 300, 300, $w, $h);
    imagejpeg($dst, $uploadPath, 90);

    imagedestroy($src);
    imagedestroy($dst);
}
    
    if (!$imageName && !empty($_POST['selected_image'])) {
        $imageName = $_POST['selected_image'];
    }

    
    $bio = htmlspecialchars($bio);

    $fields = [
        'username'  => $username,
        'firstname' => $firstname,
        'name'      => $name,
        'email'     => $email,
        'bio'       => $bio,
    ];

    if ($hashedPassword) {
        $fields['password'] = $hashedPassword;
    }

    if (!empty($imageName)) {
        $fields['image'] = $imageName;
    }

    $setParts = [];
    $params   = [];

    foreach ($fields as $col => $val) {
        $setParts[] = "$col = ?";
        $params[]   = $val;
    }

    
    $params[] = $_SESSION['user_id'];

    $sql = "UPDATE users SET " . implode(', ', $setParts) . " WHERE idUser = ?";
    $stmt = $bdd->prepare($sql);
    $stmt->execute($params);

   
    $_SESSION['email'] = $email;

    header('Location: profil.php?message=Profile updated successfully&type=success');
    exit;
}
?>
