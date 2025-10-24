<?php
session_start();

$ADMIN_PASSWORD = "change-me-please"; // ⚠️ doit être identique à admin.php
$UPLOAD_DIR = __DIR__ . "/uploads";
$META_FILE = $UPLOAD_DIR . "/metadata.json";

// Vérifier session (simple protection)
if (!isset($_SESSION['authed']) || $_SESSION['authed'] !== true) {
  http_response_code(403);
  echo "Accès refusé.";
  exit;
}

if (!file_exists($UPLOAD_DIR)) mkdir($UPLOAD_DIR, 0775, true);
if (!file_exists($META_FILE)) file_put_contents($META_FILE, "[]");

$category = $_POST['category'] ?? null;
if (!$category) { die("Catégorie manquante"); }

$allowed = ['image/jpeg' => '.jpg', 'image/png' => '.png', 'image/webp' => '.webp'];
$meta = json_decode(file_get_contents($META_FILE), true);

foreach ($_FILES['photos']['tmp_name'] as $i => $tmpPath) {
  if (!is_uploaded_file($tmpPath)) continue;
  $type = mime_content_type($tmpPath);
  if (!isset($allowed[$type])) continue;
  $ext = $allowed[$type];
  $name = bin2hex(random_bytes(8)) . $ext;
  $dest = $UPLOAD_DIR . '/' . $name;
  if (move_uploaded_file($tmpPath, $dest)) {
    $publicPath = 'uploads/' . $name;
    $meta[] = ['src' => $publicPath, 'tag' => $category];
  }
}
file_put_contents($META_FILE, json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
header("Location: admin.php");
