# Changelog

## [2.0.1] — 2026-09-29

### Corrigé
- Le logo et le favicon téléversés étaient introuvables. Les modèles du cœur
  `Image\Logo` et `Image\Favicon` écrasent le dossier de téléversement par un
  chemin écrit en dur, ignorent le `upload_dir` du `system.xml` et ajoutent un
  segment de portée : les fichiers partaient dans `pub/media/logo/default/` et
  `pub/media/favicon/default/` alors que le module les servait depuis
  `pub/media/maxcode/adminbranding/`. Deux tests verrouillent désormais ce choix.
- Le téléversement d'un favicon au format ICO échouait sur « File validation
  failed. Check Image Processing Settings in the Store Configuration. »
  L'uploader du cœur fait ouvrir toute image reconnue par l'adaptateur d'image
  configuré, et GD — l'adaptateur par défaut — ne sait pas lire l'ICO. Le champ
  reçoit maintenant son propre uploader, qui vérifie la structure du fichier
  (l'ICONDIR) au lieu de l'ouvrir. Les autres téléversements du site gardent le
  chemin de validation du cœur.

### À faire après la mise à jour
- Re-téléverser le logo et le favicon. Les valeurs déjà enregistrées désignent
  l'ancien emplacement et resteraient cassées : le correctif ne déplace pas les
  fichiers existants.

## [2.0.0] — 2026-09-17

### Ajouté
- Habillage complet de l'administration : boutons, bordures, séparateurs, rayons.
- Favicon de l'administration configurable.
- Logo appliqué à l'en-tête de l'administration, en plus de la page de connexion.
- Crédit de l'éditeur en pied de page.
- Contrat de variables CSS documenté, permettant à un module tiers de reprendre la main.
- Traductions embarquées : `i18n/en_US.csv` et `i18n/fr_FR.csv`. Les chaînes source du module sont en anglais.

### Modifié
- Le logo passe par un ViewModel : plus d'appel à l'ObjectManager dans le template.
- Le texte alternatif du logo devient configurable au lieu d'être écrit en dur.

### Supprimé
- `Block/Admin/LoginLogo.php`, jamais utilisé.
- `view/adminhtml/layout/admin_login.xml` et `view/adminhtml/templates/login/logo.phtml`,
  remplacés par une surcharge unique du bloc `logo` qui couvre la connexion et l'en-tête.

## [1.0.0]
- Logo de la page de connexion, repris du logo du site.
