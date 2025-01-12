
# Projet Symfony - Gestion des Articles et Catégories

## Prérequis

Avant de commencer, assurez-vous que vous avez les éléments suivants installés sur votre machine :

- **PHP 8.2 ou supérieur**
- **Composer**
- **Symfony CLI**
- **Base de données** : Par exemple, **MySQL** ou **PostgreSQL**

## Installation

1. **Clonez le repository** :
   Clonez ce repository dans le répertoire de votre choix sur votre machine.

   ```bash
   git clone https://github.com/votre-utilisateur/nom-du-repository.git
   cd nom-du-repository
   ```

2. **Installez les dépendances** :
   Utilisez Composer pour installer les dépendances nécessaires au projet.

   ```bash
   composer install
   ```

3. **Configurez votre base de données** :
   Dans le fichier `.env`, configurez la connexion à votre base de données :

   Pour **MySQL** :
   ```bash
   DATABASE_URL="mysql://root:root@127.0.0.1:3306/nom_de_la_base_de_donnees"
   ```

   Pour **PostgreSQL** :
   ```bash
   DATABASE_URL="pgsql://root:root@127.0.0.1:5432/nom_de_la_base_de_donnees"
   ```

4. **Créez et appliquez la base de données** :
   Créez la base de données et appliquez les migrations pour créer les tables.

   ```bash
   php bin/console make:migration
   php bin/console doctrine:migrations:migrate
   ```

5. **Charger les fixtures** :
   Chargez des données de test dans la base de données (fixtures en yaml.

   ```bash
   php bin/console hautelook:fixtures:load
   ```

### Identifiants de connexion pour les fixtures :

Lors de l'exécution de la commande `doctrine:fixtures:load`, les utilisateurs suivants seront créés par défaut :

- **Utilisateur administrateur (ROLE_ADMIN) :**
   - **Email** : `admin@example.com`
   - **Mot de passe** : `motdepasse`

- **Utilisateur utilisateur normal (ROLE_USER) :**
   - **Email** : `user@example.com`
   - **Mot de passe** : `coucou`

- **Utilisateur banni (ROLE_BANNED) :**
   - **Email** : `banned@example.com`
   - **Mot de passe** : `coucou`

### Accéder à l'application

1. **Démarrer le serveur local** :
   Si vous utilisez Symfony CLI, vous pouvez démarrer un serveur local avec la commande suivante :

   ```bash
   symfony server:start
   ```

   Ensuite, ouvrez votre navigateur et accédez à [http://127.0.0.1:8000](http://127.0.0.1:8000).