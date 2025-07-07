# Gazeta Internetowa – Instalacja i Konfiguracja

## Wymagania

- **PHP**: 8.1 lub wyższy  
- **Composer**: najnowsza wersja  
- **MySQL**: 8.0 lub wyższy  
- **Docker & Docker Compose**

---

## Instalacja z Dockerem

### Automatyczna instalacja

    git clone https://github.com/Logarzesrem/gazeta-internetowa
    cd gazeta-internetowa
    docker-compose up -d
    docker exec symfony_app composer install
    docker exec symfony_app ./setup.sh

### Ręczna instalacja z użyciem Dockera

    docker-compose up -d
    docker exec symfony_app composer install
    docker exec symfony_app php bin/console doctrine:database:create --if-not-exists
    docker exec symfony_app php bin/console doctrine:migrations:migrate --no-interaction
    docker exec symfony_app php bin/console doctrine:fixtures:load --no-interaction

## Dostęp

    Aplikacja: http://localhost:8000

    Baza danych (MySQL): localhost:3306

### Domyślni użytkownicy (po załadowaniu fixtures)

Administrator

    Email: admin@example.com

    Hasło: admin123

Użytkownicy

    Email: user@example.com, user2@example.com

    Hasło: password123

## Dodatkowe komendy

Dostęp do kontenera Docker:

    docker exec symfony_app bash

Sprawdzenie jakości kodu:

    docker exec symfony_app ./check_code_ztp2.sh

Uruchomienie testów:

    docker exec symfony_app vendor/bin/phpunit

Generowanie dokumentacji:

    docker exec symfony_app vendor/bin/phpdoc
