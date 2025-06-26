# Instalacja i Konfiguracja – Gazeta Internetowa
Wymagania

PHP: 8.1 lub wyższy
Composer: Najnowsza wersja
MySQL: 8.0 lub wyższy
Docker & Docker Compose

# Instalacja z Dockerem
## Automatyczna Instalacja
    git clone <url-repozytorium>
    cd gazeta-internetowa
    ./setup-docker.sh

## Ręczna Instalacja Docker
    docker-compose up -d
    docker exec symfony_app composer install

Aplikacja dostępna pod adresem: http://localhost:8000
Baza danych: localhost:3306

# Instalacja Lokalna (bez Dockera)
Sklonuj repozytorium:
    git clone <url-repozytorium>
    cd gazeta-internetowa

# Zainstaluj zależności:
    composer install

> Katalog vendor/ nie jest dołączony – uruchom composer install po sklonowaniu.

# Skonfiguruj bazę danych:
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    php bin/console doctrine:fixtures:load --no-interaction

# Uruchom aplikację:
Symfony CLI:
    symfony server:start

# Lub wbudowany serwer PHP:
    php -S localhost:8000 -t public/

# Domyślni Użytkownicy (po załadowaniu fixtures)
Administrator:
Email: admin@example.com
Hasło: admin123

# Użytkownicy testowi:
Email: user1@example.com, user2@example.com, ...
Hasło: password123

# Dodatkowe Komendy
Dostęp do powłoki kontenera Docker:
  docker exec -it symfony_app bash
Sprawdzenie jakości kodu:
  ./check_code_ztp2.sh
