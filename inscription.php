
<?php 
    session_start();
    $link = 'style_inscriptionn.css';
    $link2 = '';
    $script = 'inscription.js';
    $titre = 'Sign up';
    include '../Includes/header_index.php';
?>

<?php
if (isset($_SESSION['user_id']) || !empty($_SESSION['user_id'])) {
    header('location: index.php');
    exit;
}
?>
<main class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">

    <div class="signup-card rounded-4 shadow-lg w-100" style="max-width:900px;">

        <div class="row w-100 g-0">

            <div class="col-md-6 d-flex align-items-center justify-content-center p-5 text-white">
                <form action="verification_inscriptions.php" method="POST" id="signup-form" class="w-100" style="max-width:400px;">
        
                <h1 class="mb-4 text-center fw-bold">Sign Up</h1>

                 <?php if (isset($_SESSION['error'])): ?>
                            <p class="text-danger mb-3"><?= htmlspecialchars($_SESSION['error']) ?></p>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>

                    <div id="etape1" class="etape etape-active">
                        <div class="form-group">
                            <input type="text" name="username" placeholder="Username" class="form-control bg-gray text-white border-0 rounded-4"
                                value="<?= isset($_SESSION['old_data']['username']) ? htmlspecialchars($_SESSION['old_data']['username']) : '' ?>">
                            <div class="error-message" id="error-username"></div>
                        </div>

                        <div class="form-group">
                            <input type="email" name="email" placeholder="Email" class="form-control bg-gray text-white border-0 rounded-4"
                                value="<?= isset($_SESSION['old_data']['email']) ? htmlspecialchars($_SESSION['old_data']['email']) : '' ?>">
                            <div class="error-message" id="error-email"></div>
                        </div>

                        <div class="form-group">
                            <input type="password" name="mdp" class="form-control bg-gray text-white border-0 rounded-4" placeholder="Password">
                            <div class="error-message" id="error-mdp"></div>
                        </div>
                    </div>

                    <div id="etape2" class="etape">
                        <div class="form-group">
                            <input type="text" name="name" placeholder="Last Name" class="form-control bg-gray text-white border-0 rounded-4"
                                value="<?= isset($_SESSION['old_data']['name']) ? htmlspecialchars($_SESSION['old_data']['name']) : '' ?>">
                            <div class="error-message" id="error-name"></div>
                        </div>

                        <div class="form-group">
                            <input type="text" name="firstname" placeholder="First Name" class="form-control bg-gray text-white border-0 rounded-4"
                                value="<?= isset($_SESSION['old_data']['firstname']) ? htmlspecialchars($_SESSION['old_data']['firstname']) : '' ?>">
                            <div class="error-message" id="error-firstname"></div>
                        </div>

                        <div class="form-group">
                            <input type="date" name="date_of_birth" placeholder="Date of birth" 
                                class="form-control bg-gray text-white border-0 rounded-4"
                                value="<?= isset($_SESSION['old_data']['date_of_birth']) ? htmlspecialchars($_SESSION['old_data']['date_of_birth']) : '' ?>"
                                max="<?= date('Y-m-d') ?>">
                            <div class="error-message" id="error-date"></div>
                        </div>
                    </div>

                    <div id="etape3" class="etape">
                        <div class="form-group">
                            <input type="password" name="mdp-confirm" class="form-control bg-gray text-white border-0 rounded-4" placeholder="Confirm Password">
                            <div class="error-message" id="error-mdp-confirm"></div>
                        </div>

                        <div id="error-general" class="error-general" style="display:none; color:red; margin:10px 0;"></div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" id="back" class="btn btn-outline-light ">Back</button>
                        <button type="button" id="next" class="btn btn-light">Next</button>
                        <button type="submit" id="submit-btn" class="btn btn-success">Sign up</button>
                    </div>
                </form>
            </div>

            <div class="col-md-6 position-relative p-0">
                <div class="h-100 w-100 rounded-end-4 bg-cover bg-center"
                    style="background-image: url('https://i.pinimg.com/564x/33/aa/c6/33aac6ca36865e2ba573a7c28913fa54.jpg'); height:100%;">
                </div>

                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                    <span class="text-white fw-bold fs-4 text-center mb-3">Already a member?</span>
                    <a href="connexion.php" class="btn btn-danger" id="sign-in-btn">Sign In</a>
                </div>
            </div>
        </div>
    </div>

</main>


<?php include '../Includes/footer.php'?>