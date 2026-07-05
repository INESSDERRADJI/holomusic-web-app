<?php
session_start();
include '../Includes/connexion_bdd.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('location: connexion.php?error=You must log in to your account in order to post your article!');
    exit;
}

//fonction importante pour securiser le body
function containsForbiddenHtml(string $html): bool
{
    $decoded = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    if (preg_match('~<\s*(script|iframe|object|embed|link|meta|style)\b~i', $decoded)) {
        return true;
    }
    if (preg_match('~\bon\w+\s*=~i', $decoded)) {
        return true;
    }
    if (preg_match('~\b(?:javascript|vbscript)\s*:~i', $decoded)) {
        return true;
    }
    return false;
}

$error = "";

$title = "";
$bodyRaw = "";
$category = 0;

//method post formulaire et verifications
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $bodyRaw = $_POST["body"] ?? "";
    $category = intval($_POST["category"] ?? 0);
    $userId = intval($_SESSION["user_id"]);

    if ($title === "" || trim($bodyRaw) === "" || $category <= 0) {
        $error = "All fields must be filled.";
    }

    if (empty($error) && mb_strlen($title, 'UTF-8') > 60) {
        $error = "Title is too long.";
    }

    if (empty($error) && containsForbiddenHtml($bodyRaw)) {
        $error = "Forbidden HTML content detected.";
    }

    if (empty($error)) {
        $catCheck = $bdd->prepare("SELECT idCategory FROM category WHERE idCategory = ?");
        $catCheck->execute([$category]);
        if ($catCheck->rowCount() === 0) {
            $error = "Invalid category selected.";
        }
    }

    if (empty($error)) {
        if (!isset($_FILES["image"]) || $_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
            $error = "You must upload an image.";
        }
    }

    if (empty($error)) {
        $imgName = $_FILES["image"]["name"];
        $tmpName = $_FILES["image"]["tmp_name"];
        $imgSize = (int)$_FILES["image"]["size"];

        $ext = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
        $allowedExt = ["jpg", "jpeg", "png", "gif", "webp"];

        if (!in_array($ext, $allowedExt, true)) {
            $error = "Only JPG, PNG, GIF and WEBP images are allowed.";
        }

        if (empty($error) && $imgSize > 3 * 1024 * 1024) {
            $error = "Your image is too large.";
        }

        if (empty($error)) {
            $newImgName = uniqid("ART-", true) . "." . $ext;
            $uploadPath = "../uploads/" . $newImgName;

            if (!move_uploaded_file($tmpName, $uploadPath)) {
                $error = "Image upload failed.";
            }
        }

        if (empty($error)) {
            $q = $bdd->prepare("
                INSERT INTO article (title, body, date_of_pub, image, User_id, Category_id)
                VALUES (?, ?, NOW(), ?, ?, ?)
            ");
           try {
                $q->execute([$title, $bodyRaw, $newImgName, $userId, $category]);
                header("Location: article_post.php?success=1");
                exit;
            } catch (PDOException $e) {
                $error = "Database error: please remove emojis/special characters.";
            }

        }
    }
}

$titre = 'Articles Publication';
$link = 'Style_article_poste.css';
$link2 = '';
$script = '';
include '../Includes/header_index.php';
?>

<!-- Formulaire HTML ici -->
<div class="container py-5" style="max-width: 900px;">
    <h1 class="text-white mb-4">Add your article</h1>

    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="alert alert-success">Your article has been successfully published!</div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data" class="text-white"
      onsubmit="if(window.tinymce){tinymce.triggerSave();}
                const b=this.querySelector('button[type=submit]'); if(b){b.disabled=true;}">

        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control mb-3" placeholder="Enter the title"
               value="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">

        <label class="form-label">Content</label>
        <textarea id="body" name="body" class="form-control mb-3" rows="10"><?= htmlspecialchars($bodyRaw, ENT_QUOTES, 'UTF-8') ?></textarea>

        <label class="form-label">Category</label>
        <select name="category" class="form-select mb-3">
            <?php
            $req = $bdd->query("SELECT * FROM category ORDER BY name ASC");
            while ($cat = $req->fetch(PDO::FETCH_ASSOC)) {
                $selected = ($category === (int)$cat["idCategory"]) ? "selected" : "";
                echo '<option value="' . (int)$cat["idCategory"] . '" ' . $selected . '>' . htmlspecialchars($cat["name"], ENT_QUOTES, 'UTF-8') . '</option>';
            }
            ?>
        </select>

        <label class="form-label">Upload image</label>
        <input type="file" name="image" class="form-control mb-4" accept=".jpg,.jpeg,.png,.gif,.webp">

        <div class="text-center">
            <button type="submit" class="btn btn-publish">Publish Article</button>
        </div>
    </form>
</div>

<script>
tinymce.init({
  selector: '#body',
  license_key: 'gpl',   // la licence self-hosted (Community)
  plugins: 'lists link image table code fullscreen preview wordcount',
  toolbar: `
    undo redo |
    formatselect |
    bold italic underline |
    alignleft aligncenter alignright |
    bullist numlist |
    link image table |
    code fullscreen
  `,
  menubar: 'file edit view insert format tools help',
  height: 400,
  invalid_elements: 'script,iframe,object,embed,link,meta,style'
});
</script>

<?php include('../Includes/footer.php'); ?>
