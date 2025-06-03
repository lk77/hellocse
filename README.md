# Test technique HelloCse

## Prérequis

- WSL
- Docker

## Installation via WSL

- Installez les dépendances composer :
````bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
````

- Lancez les containers :
````bash
sail up -d
````

- Completez le fichier .env à l'aide du fichier .env.example

- Lancez les migrations et les seeders :
````bash
sail art migrate --seed
````

## Execution des tests

Lancez les tests via :

````bash
sail test
````

### Analyze statique

Lancez phpstan via :
````bash
sail bin phpstan
````
