<?php
// --- Configuration ---
$ADMIN_PASSWORD = "laurie123"; // ⚠️ À changer après déploiement
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
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Administration – Laurie Buyens</title>
  <link rel="stylesheet" href="styles.css" />
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial;
      background: linear-gradient(145deg, #f8f8f8, #ffffff);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    header {
      position: sticky;
      top: 0;
      width: 100%;
      background: rgba(255,255,255,0.8);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(0,0,0,0.05);
      text-align: center;
      padding: 20px 0;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    main {
      width: 100%;
      max-width: 1000px;
      padding: 40px 20px 80px;
      display: flex;
      flex-direction: column;
      gap: 40px;
    }

    .card {
      background: rgba(255,255,255,0.65);
      backdrop-filter: blur(15px) saturate(1.3);
      border-radius: 18px;
      padding: 30px 28px;
      border: 1px solid rgba(255,255,255,0.7);
      box-shadow: 0 8px 25px rgba(0,0,0,0.06);
    }

    h2, h3 {
      font-weight: 600;
      margin-bottom: 18px;
      color: #111;
    }

    label {
      display: block;
      margin-bottom: 6px;
      font-weight: 500;
      color: #333;
    }

    input[type="file"], input[type="text"], select, textarea {
      width: 100%;
      padding: 10px 12px;
      border-radius: 10px;
      border: 1px solid #ccc;
      font-size: 0.95rem;
      margin-bottom: 12px;
      background: rgba(255,255,255,0.85);
      transition: border 0.2s ease, box-shadow 0.2s ease;
    }

    input[type="file"]:focus, input[type="text"]:focus, textarea:focus, select:focus {
      border-color: #111;
      box-shadow: 0 0 0 3px rgba(0,0,0,0.05);
      outline: none;
    }

    button {
      background: #111;
      color: #fff;
      border: none;
      border-radius: 12px;
      padding: 10px 22px;
      font-size: 0.95rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.25s ease;
    }

    button:hover {
      background: #000;
      transform: translateY(-1px);
    }

    .row {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }

    .row > div {
      flex: 1;
      min-width: 260px;
    }

    .logout {
      text-align: center;
      margin-top: 40px;
    }

    .logout a {
      text-decoration: none;
      color: #666;
      font-size: 0.9rem;
      transition: color 0.2s;
    }

    .logout a:hover {
      color: #000;
    }

    /* ✅ Responsive */
    @media (max-width: 600px) {
      main {
        padding: 20px 14px 60px;
      }
      .card {
        padding: 22px 20px;
      }
      button {
        width: 100%;
      }
    }
  </style>
</head>

<body>
  <header>
    Panneau d’administration – Laurie Buyens
  </header>

  <main>
    <div class="card">
      <h3>Charger de nouvelles photos</h3>
      <form action="upload.php" method="post" enctype="multipart/form-data" class="row">
        <div>
          <label>Fichiers</label>
          <input type="file" name="photos[]" multiple required>
        </div>
        <div>
          <label>Catégorie</label>
          <select name="categorie" required>
            <option value="">Choisir une catégorie</option>
            <option value="Soirée">Soirée</option>
            <option value="Bijoux">Bijoux</option>
            <option value="Nourriture">Nourriture</option>
            <option value="Mariage">Mariage</option>
          </select>
        </div>
        <div style="align-self: end;">
          <button type="submit">Uploader</button>
        </div>
      </form>
    </div>

    <div class="card">
      <h3>Modifier la page “À propos”</h3>
      <form action="update_about.php" method="post" enctype="multipart/form-data" class="row">
        <div style="flex:1;">
          <label>Texte de présentation</label>
          <textarea name="texte" rows="6" placeholder="Votre description..."></textarea>
        </div>
        <div>
          <label>Photo de profil</label>
          <input type="file" name="photo" accept="image/*">
        </div>
        <div style="align-self:end;">
          <button type="submit">Enregistrer</button>
        </div>
      </form>
    </div>

    <div class="logout">
      <a href="logout.php">Se déconnecter</a>
    </div>
  </main>
</body>
<script>
  // Message de confirmation visuel
  if (window.location.search.includes("success")) {
    alert("Les changements ont été enregistrés avec succès !");
  }
</script>
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
    <div class="card">
    <h3>Modifier la page “À propos”</h3>
    <form action="update_about.php" method="post" enctype="multipart/form-data" class="row">
      <div style="flex:1;">
        <label>Texte de présentation</label><br/>
        <textarea name="texte" rows="6" style="width:100%;padding:10px;border-radius:10px;border:1px solid #ddd;"></textarea>
      </div>
      <div>
        <label>Photo de profil (facultatif)</label><br/>
        <input type="file" name="photo" accept="image/*" />
      </div>
      <div style="align-self:end;">
        <button type="submit">Enregistrer</button>
      </div>
    </form>
  </div>
</body>
</html>
