# Changelog

## [Non publié]

### Corrigé
- Le chocolat du thème (`#514943`) n'est pas dans la charte ; deux endroits
  y échappaient encore :
  - **Boutons `.action-secondary` hors pied de modale** (ex. « Précédent » de
    l'écran de réception d'une commande fournisseur) : surface crème, texte de
    la charte, angles arrondis, comme les autres boutons secondaires. Aussi
    retirés : l'ombre portée du texte, conçue pour un fond sombre, et la flèche
    blanche des boutons à menu (`.actions-split .action-toggle`), qui aurait
    disparu sur crème. Les boutons qui portent aussi `.action-tertiary` (fond
    transparent voulu) sont exclus.
  - **Pied de tableau des grilles** (`.data-grid tfoot th`, ex. Sous-total,
    TVA, Montant global) : le thème peint tous les `<th>` en chocolat, la
    section « en-têtes » n'atteignait que ceux du `<thead>`. Surface crème,
    cadre et texte de la charte.
- Barre « Annuler / Enregistrer » au-dessus d'une grille en édition (module
  Amasty Product Grid, édition multiple du thème) : le thème donne au panneau
  `margin-bottom: -2rem`, qui remonte la grille sur la barre ; l'en-tête
  marine mordait de 1rem sur le bas du bouton « Save Changes ». Le
  remplissage bas de la barre passe de 1rem à 3.6rem (compensation de 2rem +
  1.6rem d'air). Mesure : bas du bouton de −10 px à +16 px sous l'en-tête. La
  barre est masquée hors édition : rien ne bouge le reste du temps.

Admin Branding PRO n'est pas touché : il ne fait que poser des variables, ces
règles reprennent les siennes (`--mx-admin-surface-muted`, `--mx-admin-text`,
`--mx-admin-border-color`).

## [2.0.4] — 2026-10-07

### Modifié
- `composer.json` : description bilingue anglais / français (affichée telle quelle sur
  Packagist), « Magento 2 » cité dans les deux langues. Aucun code modifié.

### Documentation
- Page de présentation GitHub : `README.md` en anglais et `README.fr.md` en
  français, avec captures avant / après, installation et comparatif avec
  Admin Branding PRO. Le contrat des variables CSS est désormais aussi en
  anglais. Aucun code modifié.
- Installation directe depuis Packagist.
- Captures dans `docs/images/`, exclues de l'archive Composer
  (`.gitattributes`).

## [2.0.3] — 2026-10-01

### Corrigé
- Anciennes grilles (ex. Système > Notifications) : la case de sélection
  globale de l'en-tête était décalée de 2rem vers la droite, débordait sur la
  colonne suivante (qui lui masquait un coin) et remontait. Le thème la
  positionne depuis la barre d'outils, calée sans rembourrage ; nos 1.2rem 2rem
  de rembourrage et la bordure basse de cette barre sont désormais compensés.

## [2.0.2] — 2026-10-01

### Corrigé
- Les volets latéraux (`.modal-slide`) ne défilaient plus : impossible
  d'atteindre le bas de la fenêtre « Ajouter / Mettre à jour l'adresse » de la
  fiche client, ni de tout volet plus haut que l'écran. Le `overflow: hidden`
  posé sur `.modal-inner-wrap` pour rogner l'arrondi écrasait, à spécificité
  égale, le `overflow-y: auto` du thème, qui fait défiler ces volets. Le
  défilement vertical leur est rendu ; l'arrondi reste rogné.
- Multi-sélections désactivées de la configuration (case « Utiliser les valeurs
  système » cochée, ex. « Autoriser les pays ») : les options sélectionnées
  s'affichaient en blanc sur fond quasi blanc. `styles-old.css` du thème leur
  donne un texte blanc sur bande grise, et notre surlignage des options
  sélectionnées recouvrait la bande sans reprendre la couleur du texte. Elles
  passent en texte de marque, gras, sur une bande neutre (contraste 12,4:1) ;
  l'opacité 0,5 du thème est retirée de ces seuls champs, aucun couple de
  couleurs ne pouvant atteindre 4,5:1 à demi-opacité.
- Multi-sélections actives : juste après un clic, l'option choisie
  s'affichait en blanc sur notre bande claire. Chrome peint le texte
  sélectionné d'une liste ayant le focus avec sa couleur système, sans tenir
  compte de `color`. `-webkit-text-fill-color` garde le texte de marque dans
  tous les états. La bande est teintée de la couleur de coche, et le texte est
  en graisse 600, pour que la sélection se voie (contraste 13:1).

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
