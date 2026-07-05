<?php
session_start();
include '../Includes/connexion_check.php';
$link = 'style_profil.css';
$script = "profil.js";
$titre = 'Profil';
include '../Includes/header_index.php';
include '../Includes/connexion_bdd.php';

$q = 'SELECT username, name, firstname, email, bio, image FROM users WHERE idUser = ?';
$req = $bdd->prepare($q);
$req->execute([$_SESSION['user_id']]);   
$user = $req->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo '<p class="text-danger text-center mt-5">User not found</p>';
    exit;
}
?>

<main>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="card profile-card shadow-lg p-4">
                <div class="row g-4">

                    <div class="col-md-6">
                        <h3 class="text-center mb-4 fw-bold">My Profile</h3>
                        <form action="verification_modif_profilUser.php" id="profile-form" method="POST" enctype="multipart/form-data">
                            <?php if (isset($_GET['message'])): ?>
                                <div class="<?php echo ($_GET['type'] === 'success') ? 'message-success' : 'message-danger'; ?>">
                                    <?php echo htmlspecialchars($_GET['message']); ?>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>">
                            </div>

                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>">
                            </div>

                            <div class="mb-3">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>">
                            </div>

                            <div class="mb-3">
                                <label>Firstname</label>
                                <input type="text" name="firstname" class="form-control" value="<?= htmlspecialchars($user['firstname']) ?>">
                            </div>

                            <div class="mb-3">
                                <label>Old Password</label>
                                <input type="password" name="old_password" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label>New Password</label>
                                <input type="password" name="password" class="form-control">
                            </div>

                            <div class="mb-4">
                                <label>Biography</label>
                                <textarea name="bio" class="form-control" maxlength="600" rows="3"><?= htmlspecialchars($user['bio']) ?></textarea>
                            </div>

                            <div class="text-center">
                                <button type="submit" id="save-btn" class="btn btn-save" style="display:none;">
                                    Save changes
                                </button>
                            </div>
                        
                    </div>

                    <div class="col-md-6 text-center">
                        <h3 class="mb-4 fw-bold">Profile Picture</h3>

                        <div class="mb-3">
                            <img id="selected-photo"
                                src="<?= !empty($user['image']) 
                                        ? '../uploads/' . htmlspecialchars($user['image']) 
                                        : '../uploads/photo_profil_default3.jpg' ?>"
                                class="img-fluid rounded mb-3"
                                style="max-height:250px;">
                        </div>

                        <div class="d-flex justify-content-around mb-3">
                            <img class="selectable-photo rounded" src="../uploads/photo_profil_default1.jpg" style="width:80px; cursor:pointer;">
                            <img class="selectable-photo rounded" src="../uploads/photo_profil_default2.jpg" style="width:80px; cursor:pointer;">
                            <img class="selectable-photo rounded" src="../uploads/photo_profil_default3.jpg" style="width:80px; cursor:pointer;">
                            <input type="hidden" name="selected_image" id="selected-image"value="<?= htmlspecialchars($user['image']) ?>">

                        </div>

                        <div class="mb-3">
                            <label for="upload-photo" class="form-label">Or upload your own:</label>
                            <input class="form-control" type="file" id="upload-photo" name="image" accept="image/png, image/jpeg, image/gif">
                        </div>
                    </div>
                </div>
            </div>
</form>
        </div>
    </div>
</div>
</main>

<?php include '../Includes/footer.php'; ?>

