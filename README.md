# Portfolio Laurie Buyens – HTML/CSS/JS (+ PHP minimal pour l'admin)

## Structure
- `index.html` : galerie mosaïque (ordre aléatoire, filtre par catégorie, lightbox plein écran, scroll infini)
- `about.html` : présentation & contact
- `styles.css` : styles globaux + boutons glassmorphism
- `script.js` : chargement progressif, filtrage, lightbox
- `admin.php` : page de connexion + interface d'upload (protégée par mot de passe)
- `upload.php` : traitement de l'upload, mise à jour du `uploads/metadata.json`
- `uploads/` : dossier des images + `metadata.json`

## Déploiement rapide
1. Hébergement avec PHP (OVH, Infomaniak, o2switch, AlwaysData, etc.).
2. Uploadez **tout** le dossier sur votre hébergeur (racine du site).
3. Ouvrez `admin.php`, connectez-vous (mot de passe par défaut **change-me-please**), et uploadez vos photos.

> ⚠️ Pensez à **changer le mot de passe** dans `admin.php` et `upload.php` (même valeur).

## Comment ça marche
- Chaque photo uploadée est enregistrée dans `/uploads` et inscrite dans `uploads/metadata.json` avec sa catégorie.
- La galerie lit ce JSON, mélange l'ordre, applique le filtre choisi et charge les images par paquets (scroll infini).
- La lightbox permet la navigation avec clics et flèches du clavier (← →), fermeture par Échap.

## Personnalisation
- Boutons / catégories : modifiez les boutons dans `index.html` et les options de `admin.php`.
- Adresse email du formulaire : remplacez `contact@example.com` dans `about.html`.
- Taille des lots : changez `BATCH_SIZE` dans `script.js`.

## Limites et sécurité
- L'authentification est **basique** (session + mot de passe en clair côté serveur). Pour un usage pro, envisagez HTTPS, mots de passe hashés, rate limiting, etc.
- Aucun redimensionnement serveur : uploadez des images déjà optimisées (2048px max conseillé).

Bon shoot ! 📸
