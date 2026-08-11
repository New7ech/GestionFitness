# Gestion-ok

Guide simple pour lancer le projet en local sur Windows 11 avec Docker Desktop.

## 1) Prerequis (machine secretariat)

- Windows 11
- Docker Desktop installe et demarre
- Connexion internet au premier lancement (telechargement des images)

## 2) Installation rapide (premiere fois)

1. Ouvrir le dossier du projet.
2. Double-cliquer `INSTALL-LOCAL.bat`.
3. Attendre la fin de l'installation.

URLs:

- Application: `http://localhost:8080`
- phpMyAdmin: `http://localhost:8081`
- MailHog: `http://localhost:8025`

Base de donnees Docker par defaut:

- Host Windows: `localhost`
- Port: `3307`
- Base: `laravel`
- User: `laravel`
- Pass: `secret`

## 3) Usage quotidien (client)

- Demarrer: double-cliquer `START-LOCAL.bat`
- Arreter: double-cliquer `STOP-LOCAL.bat`

## 4) Reinitialiser completement

Si tu veux repartir de zero (donnees supprimees):

- Double-cliquer `RESET-LOCAL.bat`
- Puis relancer `INSTALL-LOCAL.bat`

## 5) Commandes utiles (support technique)

Depuis PowerShell dans le dossier du projet:

```powershell
docker compose ps
docker compose logs app --tail=100
docker compose logs mysql --tail=100
```
Tes identifiants ont été recréés dans la base locale :
Admin
Email : admin@gestion.local
Mot de passe : Admin@1234

Manager
Email : manager@gestion.local
Mot de passe : Manager@1234
Ouvre l’application ici :
http://localhost:8080/login
