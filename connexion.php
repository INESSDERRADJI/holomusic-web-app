<?php 
$link = 'style_connexion.css';
$link2 = '';
$script = '';
$titre = 'Sign in';
include '../Includes/header_index.php';
?>

<main class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
    
    <div class="signup-card rounded-4 shadow-lg w-100" style="max-width:900px;">

        <div class="row w-100 g-0">
            <div class="col-md-6 d-flex align-items-center justify-content-center p-5 text-white">
                <form action="verification.php" method="POST" class="w-100" style="max-width:400px;">
                    
                    <h1 class="mb-4 text-center fw-bold">Sign In</h1>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email" 
                               class="form-control bg-gray text-white border-0 rounded-4">
                    </div>

                    <div class="form-group">
                        <input type="password" name="password" placeholder="Password" 
                               class="form-control bg-gray text-white border-0 rounded-4">
                    </div>

                    <?php if (isset($_GET['message'])): ?>
                        <p class="text-warning"><?= htmlspecialchars($_GET['message']) ?></p>
                    <?php endif; ?>
                    
                    <div class="mt-4">
                        <button type="submit" name="verif" class="btn btn-login-connexion w-100">
                            Login
                        </button>
                    </div>

                </form>
            </div>
            <div class="col-md-6 position-relative p-0">
                <div class="h-100 w-100 rounded-end-4 bg-cover bg-center"
                    style="background-image: url('https://i.pinimg.com/564x/33/aa/c6/33aac6ca36865e2ba573a7c28913fa54.jpg'); height:100%;">
                </div>

                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                    <span class="text-white fw-bold fs-4 text-center mb-3">New here ?</span>
                    <a href="inscription.php" class="btn btn-danger" id="sign-in-btn">Sign up</a>
                </div>
            </div>
        </div>

    </div>
</main>
<?php include '../Includes/footer.php'?> 


