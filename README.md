# Symfony for sale

## Auteur(s)
- Alexis UDYCZ
- Vincent GUILLEMOT

## Installation / Configuration

```bash
git clone https://iut-info.univ-reims.fr/gitlab/udyc0001/symfony-for-sale.git
cd symfony-for-sale
composer install
npm install
```

| Commande    | Description                                              |
|-------------|----------------------------------------------------------|
| `start`     | Lance le serveur web de test                             |
| `test:cs`   | Lance la vérification du code par PHP CS Fixer           |
| `fix:cs`    | Lance la correction du code par PHP CS Fixer             |
| `test:yaml` | Lance la vérification des fichiers de configuration YAML |
| `test:twig` | Lance la vérification des fichiers de templates TWIG     |

- Lancement de la base de données (avec docker)
```shell
docker compose up
```

## Utilisation de WebPack

```shell
npm install # Si pas encore fait
npm run watch
```

## Pages du projet

### Advertisement

#### Index

[localhost:8000/advertisement/]()

C'est l'index avec la liste de toutes les annonces paginées.

#### Show

[localhost:8000/advertisement/show/{id}]()

Sur cette page il y a les détails d'une annonce, l'id doit exister et être un integer.
#### Edit/Upload
[localhost:8000/advertisement/new]()

Amène au formulaire pour créer une nouvelle annonce.

[localhost:8000/advertisement/edit/{id}]()

Amène au formulaire pour éditer une annonce existante.
### Category

[localhost:8000/category/]()

Amène sur l'index de category, on trouve toutes les catégories du projet.

[localhost:8000/category/show/{id}]()

Amène sur les annonces d'une catégorie en particulier, spécifié par l'id de la catégorie.