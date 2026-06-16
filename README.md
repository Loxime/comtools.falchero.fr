# ComTools

Application web de gestion de communication.

## Stack

- Backend: Symfony 7
- Frontend: Nuxt 3 en mode SPA
- Base de donnees: MySQL 8
- Orchestration: Docker Compose

## Demarrage

```sh
cp .env.example .env
docker-compose up -d
```

Services locaux:

- API Symfony: http://localhost:8080
- Frontend Nuxt: http://localhost:3000
- MySQL: localhost:3306

## Verification base de donnees

```sh
docker-compose exec php php bin/console doctrine:query:sql "SELECT DATABASE()"
```

## Regle de commit

Chaque sprint doit se terminer par un commit puis un push.

Format obligatoire:

```txt
feat(scope): description courte en francais
```

Exemples:

```txt
feat(auth): ajout inscription, connexion et hash mot de passe
feat(roles): middleware admin + isolation donnees utilisateurs
feat(ui): navbar avec theme dark/light et i18n EN/FR
fix(tickets): correction isolation des tickets par user
chore(docker): ajout volume persistant pour MySQL
```

Un commit par sprint, sans commits intermediaires sauf si une migration de base de donnees est ajoutee. Dans ce cas, utiliser un commit separe avec le prefixe `db:`.
