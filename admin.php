<?php
// --- Configuration ---
$ADMIN_PASSWORD = "change-me-please"; // ⚠️ À changer après déploiement
$UPLOAD_DIR = __DIR__ . "/uploads";
$META_FILE = $UPLOAD_DIR . "/metadata.json";

session_start();
$authed = isset($_SESSION['authed']) && $_SESSION['authed'] === true;

if (isset($_POST['action']) && $_POST['action'] === 'login') {
  $pass = $_POST['password'] ?? '';
  if ($pass === $ADMIN_PASSWORD) {
    $_SESSION['authed'] = true;
    header("Location: admin.php");
    exit;
  } else {
    $error = "Mot de passe incorrect.";
  }
}

if (!$authed):
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin – Connexion</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    body{display:grid;place-items:center;height:100vh}
    form{padding:24px;border-radius:16px;border:1px solid #e5e5e5;max-width:360px;width:92%}
    input,button{width:100%;padding:10px 12px;margin-top:10px;border-radius:10px;border:1px solid #ddd}
    button{border:0;background:#111;color:#fff;cursor:pointer}
    .error{color:#c00;margin-top:10px}
  </style>
</head>
<body>
  <form method="post">
    <h2>Admin – Connexion</h2>
    <input type="password" name="password" placeholder="Mot de passe" required />
    <input type="hidden" name="action" value="login" />
    <button type="submit">Se connecter</button>
    <?php if(isset($error)) { echo "<div class='error'>".$error."</div>"; } ?>
  </form>
</body>
</html>
<?php
exit; endif;

// --- Authenticated area ---
if (!file_exists($UPLOAD_DIR)) mkdir($UPLOAD_DIR, 0775, true);
if (!file_exists($META_FILE)) file_put_contents($META_FILE, "[]");

?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Administration</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    body{padding:20px; max-width:900px; margin:0 auto}
    .card{border:1px solid #eee; border-radius:16px; padding:18px; margin-bottom:18px}
    .row{display:flex; gap:12px; flex-wrap:wrap}
    label{font-size:14px; color:#555}
    input[type=file], select{padding:10px; border:1px solid #ddd; border-radius:10px}
    button{padding:10px 14px; border-radius:12px; border:0; background:#111; color:#fff; cursor:pointer}
    .grid{display:grid; grid-template-columns:repeat(auto-fill, minmax(160px,1fr)); gap:12px}
    figure{margin:0; border:1px solid #eee; border-radius:12px; overflow:hidden; aspect-ratio:4/3; display:grid; place-items:center}
    img{width:100%; height:100%; object-fit:cover}
  </style>
</head>
<body>
  <h1>Administration</h1>

  <div class="card">
    <h3>Charger des photos</h3>
    <form action="upload.php" method="post" enctype="multipart/form-data" class="row">
      <input type="hidden" name="token" value="<?php echo session_id(); ?>">
      <div>
        <label>Fichiers (JPEG/PNG, multiples)</label><br/>
        <input type="file" name="photos[]" accept="image/*" multiple required />
      </div>
      <div>
        <label>Catégorie</label><br/>
        <select name="category" required>
          <option value="Soirée">Soirée</option>
          <option value="Bijoux">Bijoux</option>
          <option value="Nourriture">Nourriture</option>
          <option value="Mariage">Mariage</option>
        </select>
      </div>
      <div style="align-self:end">
        <button type="submit">Uploader</button>
      </div>
    </form>
    <p style="color:#666; font-size:13px">Astuce : glisser-déposer fonctionne selon l'hébergeur. Les fichiers sont stockés dans <code>/uploads</code>, et la galerie lit <code>uploads/metadata.json</code>.</p>
  </div>

  <div class="card">
    <h3>Photos existantes</h3>
    <div class="grid">
      <?php
      $meta = json_decode(file_get_contents($META_FILE), true);
      foreach ($meta as $m) {
        $src = htmlspecialchars($m['src']);
        $tag = htmlspecialchars($m['tag']);
        echo "<figure title='{$tag}'><img src='{$src}' alt='{$tag}'/></figure>";
      }
      ?>
    </div>
  </div>
</body>
</html>
