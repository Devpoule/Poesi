# Guide simple d'installation (back + front + JWT)

Ce document explique comment installer et lancer l'application sur n'importe quelle machine.
Les commandes sont generiques et peuvent etre adaptees a Windows, Linux ou macOS.

---

## 1) Prerequis

Installez PHP 8.2+ avec l'extension openssl, Composer, Symfony CLI, Node.js et npm.
Verifiez que l'extension openssl est chargee avec `php -m`.

---

## 2) Recuperer le projet

Clonez le depot puis placez-vous a la racine du projet.

```bash
git clone <REPO_URL>
cd <PROJECT_ROOT>
```

---

## 3) Installer le backend

Placez-vous dans `backend/` et installez les dependances.

```bash
cd api
composer install
```

Copiez `.env` en `.env.local` puis configurez `DATABASE_URL`.

```bash
cp .env .env.local
```

Creez la base et lancez les migrations.

```bash
symfony console doctrine:database:create
symfony console doctrine:migrations:migrate
```

Si vous voulez des comptes de test, lancez le seed.

```bash
symfony console app:seed-all
```

---

## 4) Generer les cles JWT (indispensable pour le login)

Sans cles JWT, le login renvoie une erreur 500.

```bash
cd api
mkdir config/jwt
symfony console lexik:jwt:generate-keypair --overwrite
```

Si la commande echoue avec `error:80000003`, definissez `OPENSSL_CONF`
avec le chemin vers `openssl.cnf`, puis relancez la commande.

---

## 5) Lancer le backend

```bash
cd api
symfony serve
```

Verifiez l'API sur `https://127.0.0.1:8000/health` et acceptez le certificat si besoin.

---

## 6) Installer et lancer le frontend

```bash
cd frontend
npm install
npm run web
```

L'UI est accessible sur `http://localhost:8081`.

---

## 7) Configurer l'URL API cote front

Dans `frontend/src/support/config/env.ts`, `baseUrl` doit pointer vers
`https://127.0.0.1:8000` (ou le port affiche par Symfony).

---

## 8) Se connecter

Allez sur `http://localhost:8081/login`.
Si les seeds ont ete executes, utilisez :
- `user_1@test.local` / `user`
- `admin@test.local` / `admin`

Une fois connecte, le token JWT est ajoute automatiquement.

---

## 9) Depannage rapide

- `ERR_CONNECTION_REFUSED` : le backend n'est pas lance ou le port est different.
- `https://localhost:8000` ne repond pas : utilisez `https://127.0.0.1:8000`.
- `CORS preflight blocked` : redemarrez Symfony et verifiez `OPTIONS /api/login_check`.
- `403 /api/poems` : pas de token, connectez-vous.
- `500 /api/login_check` : cles JWT manquantes ou OpenSSL mal configure.
