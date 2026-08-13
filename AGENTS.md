# AGENTS.md — Guide opérationnel pour les agents IA sur ce dépôt

Ce fichier s'applique à tout agent de codage IA (Codex, Claude Code, ou autre) intervenant sur ce dépôt. Il reste en vigueur au-delà de la transformation fitness initiale — mets-le à jour si une convention change.

---

## 1. Contexte du projet

Application de gestion pour un centre fitness spécialisé dans la **perte de poids** et la **rééducation de la diastasie**. Le dépôt a été créé à l'origine comme un ERP de gestion de stock/facturation (nom historique : « e-Gstion »). Ce module de stock/facturation (Articles, Catégories, Emplacements, Fournisseurs, Factures) **reste actif et n'est pas lié** au domaine fitness — les deux cohabitent dans la même application.

Le domaine fitness gère le cycle : inscription → paiement → reçu → challenge (perte de poids ou diastasie) → présences → mesures → photos/vidéos → progression → bilan final.

---

## 2. Stack technique

- **Laravel 12** (`^12.0`), **PHP 8.2+** (exécuté en 8.3 dans Docker).
- **MySQL 8.0** en développement/production, **SQLite en mémoire** pour les tests (`phpunit.xml`).
- **Dompdf** (`barryvdh/laravel-dompdf`) pour tous les PDF.
- **Spatie Laravel Permission** pour les rôles/permissions.
- **Frontend : kaiadmin (Bootstrap 5 + jQuery + DataTables + Chart.js + SweetAlert)**, servi en assets statiques (`public/assets/`). **Tailwind/Vite sont installés mais non chargés par aucune vue — ne pas les utiliser** pour de nouvelles pages ; rester sur kaiadmin pour la cohérence visuelle.
- **PHPUnit** (pas Pest), attributs `#[Test]`.
- **Docker** : nginx + php-fpm + MySQL + Redis + Mailhog (dev), installation scriptée pour Windows (`INSTALL-LOCAL.bat`).

---

## 3. Architecture

- MVC Laravel classique, 100 % rendu serveur (Blade). Pas d'API (`routes/api.php` n'existe pas) sauf besoin explicite futur.
- Un `Service` dédié par domaine métier complexe (`FactureService` existant, `PaymentService`/`RecuService`/`DashboardService` pour le domaine fitness) — pas de logique métier lourde directement dans les contrôleurs.
- Un `FormRequest` par action de formulaire, avec `messages()` en français.
- Une `Policy` Laravel par modèle protégé, vérifiée via `$this->authorize()` — **jamais** de vérification de permission uniquement dans la vue.
- Modèles français sans accents dans le code (`Categorie`, `Recu`, `Presence`), comme la convention déjà en place.

---

## 4. Conventions Laravel

- Pagination : `->paginate(10)` à `->paginate(15)`.
- Recherche/filtres : `->when($request->filled('champ'), fn ($q) => $q->where(...))`.
- Flash messages : `->with('success', '...')` / `->with('error', '...')`.
- Statuts/types/modes fixes : **enums PHP natifs backés** (`enum X: string { case A = 'a'; }`), castés sur le modèle (`'status' => ParticipantStatus::class]`), valeurs de base de données en ASCII sans accent, libellés accentués uniquement via une méthode `label()` sur l'enum.
- Avant une suppression définitive, vérifier les dépendances (comme `CategorieController` le fait déjà) ou préférer un `SoftDelete` pour toute donnée historique/financière.
- Style de code : `./vendor/bin/pint` (préréglage par défaut, pas de `pint.json` custom actuellement — ne pas en ajouter un sans le justifier).

---

## 5. Règles de base de données

- Toute nouvelle table historique (paiements, reçus, mesures, présences, médias, commentaires, challenges, participantes) utilise `SoftDeletes`.
- Toute colonne `created_by`/`updated_by`/`recorded_by`/`uploaded_by` : `foreignId(...)->nullable()->constrained('users')->onDelete('set null')` — **toujours avec contrainte FK réelle** (le dépôt contient une incohérence existante entre `articles` et `categories` sur ce point ; ne pas la reproduire).
- Contraintes d'unicité au niveau **base de données** quand la règle métier l'exige (ex. `UNIQUE(challenge_id, attendance_date)` sur les présences) — ne pas se reposer uniquement sur la validation applicative pour ce type de règle.
- Index sur toute colonne de filtre/tri fréquent (statuts, dates de fin, clés étrangères de recherche).
- Ne jamais stocker de colonne dénormalisée redondante sans raison documentée — exception assumée et documentée : les champs "snapshot" d'un reçu déjà émis (figés intentionnellement, cf. section 10).

---

## 6. Règles de sécurité

- Toute route métier (hors login) est protégée par `auth` **et** par une permission Spatie réellement vérifiée (Policy), jamais par un simple masquage de bouton dans la vue.
- Aucune donnée sensible (photo/vidéo de suivi, note de santé) n'est servie via une route publique. Le contrôleur `MediaController` existant (disque `public`, sans vérification d'accès) **ne doit jamais être réutilisé pour les médias participantes**.
- Validation systématique **côté serveur** de tout upload : type MIME, extension, taille max — ne jamais faire confiance à une validation HTML/JS seule.
- Nom de fichier stocké toujours généré aléatoirement, jamais le nom fourni par l'utilisateur.
- `.env` ne doit jamais être commité (actuellement une exception existe dans l'historique du dépôt — à corriger, pas à reproduire).
- Rate limiting (`throttle`) requis sur toute route d'authentification.
- Mass assignment : toujours passer par `$fillable` explicite, jamais `$guarded = []`.

---

## 7. Règles de gestion des médias

- Disque dédié non public (ex. `participant_media`, racine sous `storage/app/private/`), jamais le disque `public`.
- Un contrôleur d'accès dédié vérifie authentification + permission avant de streamer un fichier (`Storage::disk(...)->response(...)`), à l'image de la logique anti-traversal déjà présente dans `MediaController` (à réutiliser pour l'inspiration, pas pour l'exécution).
- Types autorisés et tailles maximales définis en config, pas en dur dans chaque contrôleur.
- Suppression/remplacement d'un média : `SoftDelete` uniquement, jamais de suppression physique immédiate côté application (permet une restauration en cas d'erreur).

---

## 8. Règles de permissions

- Rôles existants (`super_admin`, `manager`, `employee`, `guest`) à conserver intacts. Nouveau rôle fitness : `coach`.
- Convention de nommage des permissions : `{verbe}-{ressource}` en kebab-case, comme l'existant (`show-articles`, `edit-factures`).
- Chaque nouvelle ressource métier a sa `Policy`, enregistrée et appelée via `$this->authorize()` dans chaque méthode de contrôleur concernée.
- Les données de santé sensibles (`health_notes`, `has_cesarean`) sont protégées par une permission dédiée (`view-participante-health-data`), distincte de la permission générale de consultation d'une participante.
- Le menu latéral (`layouts/sidebar.blade.php`) doit conditionner l'affichage de toute nouvelle entrée avec `@can(...)` — l'existant ne le fait pour aucune entrée ; ne pas reproduire cette lacune sur les nouvelles entrées.

---

## 9. Règles concernant les migrations

- **Aucune migration destructive** sur les tables existantes (`users`, `articles`, `categories`, `emplacements`, `fournisseurs`, `factures`) sans justification explicite et validation préalable.
- Toute nouvelle colonne sur une table existante est `nullable` ou avec valeur par défaut, pour ne jamais casser les lignes déjà présentes.
- Les nouvelles tables du domaine fitness sont **additives** : aucune donnée fitness n'existe encore, donc aucune stratégie de migration de données n'est nécessaire pour ce domaine — uniquement pour ne pas perturber les données existantes (Users/Roles/Permissions/Articles/Factures).
- Toujours tester une migration avec `php artisan migrate:fresh` en local/CI avant de la considérer terminée.
- Ne jamais modifier une migration déjà exécutée en production/partagée — créer une nouvelle migration corrective à la place.

---

## 10. Règles de tests

- PHPUnit (pas Pest), attributs `#[Test]`, `RefreshDatabase`.
- Authentifier explicitement l'utilisateur de test avec le bon rôle/permission avant d'appeler une route protégée (cf. `ArticleManagementTest` comme référence correcte — **pas** `FactureManagementTest`/`PermissionWorkflowTest`, qui contiennent actuellement ce défaut et doivent être corrigés en priorité).
- `Storage::fake('participant_media')` (ou disque concerné) pour tout test impliquant un upload.
- Toute nouvelle fonctionnalité doit avoir : un test de création, un test de validation (cas d'échec), un test de permission (403 pour un rôle non autorisé), et pour les calculs (bilan, solde de paiement) un test avec des valeurs connues vérifiées à la main.
- La suite complète doit rester **verte** avant de clore une phase de travail — ne jamais empiler du code sur une base de tests rouge.

---

## 11. Commandes de développement

```bash
docker compose up -d --build
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
docker compose exec app php artisan test
docker compose exec app php artisan route:list
docker compose exec app php artisan migrate:status
docker compose exec app ./vendor/bin/pint

# Après ajout d'une dépendance composer : vendor/ est un volume Docker NOMMÉ
# (pas un simple bind mount) — resynchroniser explicitement :
docker compose exec app composer install
```

---

## 12. Règles UX/UI

- Rester sur kaiadmin/Bootstrap/jQuery pour toute nouvelle vue — ne pas introduire Tailwind (pipeline installé mais inactif) ni un autre framework front.
- DataTables pour les listes, pagination Laravel native, recherche + filtres au-dessus du tableau.
- SweetAlert pour les confirmations de suppression (déjà utilisé dans le projet).
- Tous les messages de validation et flash messages en français.
- Prévoir systématiquement : état de chargement, état vide, pagination, recherche, filtres, pour toute nouvelle liste.
- Toute nouvelle entrée de menu conditionnée par permission (`@can`).

---

## 13. Règles métier (résumé — détail complet dans `03-PROMPT-CURSOR-CORRECTIONS.md`)

> **Mise à jour de modèle (v2)** : `Challenge` représente désormais une **session/offre** (type, dates, capacité optionnelle, statut), créée **indépendamment et avant** toute participante. `Inscription` est l'entité qui relie une **participante** à **un challenge donné** (réservation d'une place) — c'est elle qui porte l'objectif, le tarif, le statut de paiement, et c'est elle que référencent paiements, mesures, présences et médias. Une participante peut avoir plusieurs `Inscription` au fil du temps (historique, renouvellements) ; un `Challenge` peut avoir plusieurs `Inscription` (plusieurs participantes dans la même session, dans la limite de sa capacité si elle est définie).

- Un `Challenge` (session) est créé **avant** toute participante — jamais l'inverse.
- Inscrire une participante = créer une `Inscription` qui référence une participante **et** un `Challenge` existant (`planifie` ou `en_cours`), en vérifiant la capacité disponible si elle est définie (avertissement, pas de blocage strict).
- Le « renouvellement » d'une participante = une nouvelle `Inscription` (vers un `Challenge` existant ou nouvellement créé), jamais une modification de l'inscription précédente.
- Tout ce qui est daté et personnel (paiements, présences, mesures, médias, objectifs, bilan) est rattaché à l'**inscription**, jamais directement à la participante, et jamais au challenge partagé (qui peut concerner plusieurs participantes à la fois).
- Seules les informations permanentes (identité, antécédents de santé déclarés) vivent sur la fiche participante.
- `challenges.end_date` est **toujours calculée** (start_date + durée), jamais saisie manuellement. Les dates effectives d'une inscription sont toujours celles de son challenge (pas de duplication de dates sur l'inscription).
- `inscriptions.payment_status` est **toujours recalculé** à partir de ses paiements, jamais édité manuellement.
- Une mesure existante n'est **jamais écrasée** — historique intégral conservé.
- Une seule présence par inscription et par date — contrainte base de données.
- Chaque paiement, présence, mesure, média enregistre l'utilisateur qui l'a créé/modifié.

---

## 14. Fichiers importants


| Fichier/dossier | Rôle |
|---|---|
| `app/Services/FactureService.php` | Modèle de référence pour la logique métier transactionnelle (à imiter pour `PaymentService`/`RecuService`) |
| `app/Http/Controllers/MediaController.php` | Exemple de garde anti-traversal — **ne pas réutiliser tel quel** pour les médias participantes (absence d'authentification) |
| `database/seeders/ImproveRolesAndPermissionsSeeder.php` | Seeder des rôles/permissions — à étendre, jamais à remplacer |
| `resources/views/layouts/sidebar.blade.php` | Menu latéral — ajouter les nouvelles entrées en les conditionnant par permission |
| `resources/views/factures/pdf.blade.php` | Gabarit de référence pour tout nouveau PDF (reçu, bilan) |
| `tests/Feature/ArticleManagementTest.php` | Bon exemple de test authentifié — référence à suivre |
| `tests/Feature/FactureManagementTest.php`, `PermissionWorkflowTest.php` | Contiennent le défaut d'authentification manquante à corriger en priorité |
| `docker-compose.yml`, `dockerfile`, `docker-entrypoint.sh` | Fonctionnement local — ne pas modifier sans nécessité |
| `.env` (versionné actuellement) | À retirer du suivi Git (`git rm --cached .env`) sans supprimer le fichier local |

---

## 15. Ce qu'un agent doit vérifier AVANT de modifier le code

- Lire le fichier concerné en entier (modèle, contrôleur, migration, vue) avant de le modifier — ne jamais supposer son contenu.
- Vérifier qu'aucune fonctionnalité existante ne dépend du comportement qu'on s'apprête à changer (`grep` des usages).
- Vérifier l'état actuel de la suite de tests (`php artisan test`) pour distinguer une régression introduite d'un défaut préexistant.
- Vérifier les permissions/rôles déjà seedés avant d'en ajouter de nouveaux (éviter les doublons de nom).

## 16. Ce qu'un agent doit vérifier APRÈS modification

- `php artisan migrate:fresh --seed` sans erreur.
- Suite de tests complète verte.
- `php artisan route:list` : pas de route dupliquée, aucune route existante disparue.
- Pour toute nouvelle route protégée : test explicite du 403 (rôle non autorisé) et du succès (rôle autorisé).
- `git diff` relu intégralement — aucune modification non expliquée hors du périmètre de la tâche.
- Navigation manuelle rapide des nouvelles vues pour vérifier la cohérence visuelle avec kaiadmin.

---

## 17. Interdictions importantes

- Ne jamais écraser une mesure existante — toujours créer une nouvelle entrée ou passer par une suppression douce tracée.
- Ne jamais servir un média participante via une route non authentifiée.
- Ne jamais introduire Tailwind ou un autre framework CSS dans les nouvelles vues.
- Ne jamais faire de migration destructive sur une table existante sans validation explicite préalable.
- Ne jamais valider uniquement côté client (JS) sans validation serveur équivalente.
- Ne jamais committer `.env`, une clé API, ou tout secret.
- Ne jamais laisser une permission Spatie semée sans vérification côté serveur correspondante.
- Ne jamais supprimer le compte de l'utilisateur actuellement connecté (règle déjà en place, à préserver).
- Ne jamais dupliquer un numéro de reçu, y compris après suppression douce.

---

## 18. Procédure de validation finale (avant de considérer une tâche terminée)

1. `php artisan migrate:fresh --seed` → aucune erreur.
2. `php artisan test` (ou `vendor/bin/phpunit`) → 100 % vert.
3. `php artisan route:list` → cohérent, sans doublon ni régression.
4. Revue des permissions : chaque nouvelle route testée en 403 (non autorisé) et en succès (autorisé).
5. Revue de sécurité média : aucun fichier participante accessible sans authentification.
6. `git diff` relu en entier, changements documentés dans le message de commit.
7. Mise à jour de ce fichier `AGENTS.md` si une convention a changé pendant la tâche.
