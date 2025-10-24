<?php
session_start();

if (!isset($_SESSION['authed']) || $_SESSION['authed'] !== true) {
  http_response_code(403);
  echo "Accès refusé.";
  exit;
}

$UPLOAD_DIR = __DIR__ . "/uploads";
$ABOUT_FILE = $UPLOAD_DIR . "/about.json";

if (!file_exists($UPLOAD_DIR)) mkdir($UPLOAD_DIR, 0775, true);

$texte = trim($_POST['texte'] ?? '');
$photoPath = null;

if (!empty($_FILES['photo']['tmp_name'])) {
  $allowed = ['image/jpeg' => '.jpg', 'image/png' => '.png', 'image/webp' => '.webp'];
  $type = mime_content_type($_FILES['photo']['tmp_name']);
  if (isset($allowed[$type])) {
    $ext = $allowed[$type];
    $name = 'profil' . $ext;
    move_uploaded_file($_FILES['photo']['tmp_name'], $UPLOAD_DIR . '/' . $name);
    $photoPath = 'uploads/' . $name;
  }
}

$data = [];
if (file_exists($ABOUT_FILE)) {
  $data = json_decode(file_get_contents($ABOUT_FILE), true) ?? [];
}

if ($texte) $data['texte'] = $texte;
if ($photoPath) $data['photo'] = $photoPath;

file_put_contents($ABOUT_FILE, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

header("Location: admin.php");
exit;
?>
