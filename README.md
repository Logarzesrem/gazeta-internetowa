# Instalacja i Konfiguracja – Gazeta Internetowa

## Wymagania

- **PHP:** 8.1 lub wyższy
- **Composer:** Najnowsza wersja
- **MySQL:** 8.0 lub wyższy
- **Docker & Docker Compose**

## Instalacja z Dockerem

### Automatyczna Instalacja
```bash
git clone https://github.com/Logarzesrem/gazeta-internetowa
cd gazeta-internetowa
docker-compose up -d
docker exec symfony_app ./setup.sh
```

### Ręczna Instalacja Docker
```bash
docker-compose up -d
docker exec symfony_app composer install
docker exec symfony_app php bin/console doctrine:database:create --if-not-exists
docker exec symfony_app php bin/console doctrine:migrations:migrate --no-interaction
docker exec symfony_app php bin/console doctrine:fixtures:load --no-interaction
```

**Aplikacja dostępna pod adresem:** http://localhost:8000  
**Baza danych:** localhost:3306

## Instalacja Lokalna (bez Dockera)

### Sklonuj repozytorium:
```bash
git clone https://github.com/Logarzesrem/gazeta-internetowa
cd gazeta-internetowa
```

### Zainstaluj zależności:
```bash
composer install
```

> **Katalog vendor/ nie jest dołączony** – uruchom `composer install` po sklonowaniu.

### Skonfiguruj bazę danych:
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load --no-interaction
```

### Uruchom aplikację:

**Symfony CLI:**
```bash
symfony server:start
```

**Lub wbudowany serwer PHP:**
```bash
php -S localhost:8000 -t public/
```

## Domyślni Użytkownicy (po załadowaniu fixtures)

### Administrator:
- **Email:** `admin@example.com`
- **Hasło:** `admin123`

### Użytkownik:
- **Email:** `user@example.com`, `user2@example.com`
- **Hasło:** `password123`

## Dodatkowe Komendy

**Dostęp do powłoki kontenera Docker:**
```bash
docker exec symfony_app bash
```

**Sprawdzenie jakości kodu:**
```bash
docker exec symfony_app ./check_code_ztp2.sh
```

**Uruchomienie testów:**
```bash
docker exec symfony_app vendor/bin/phpunit
```

**Generowanie dokumentacji:**
```bash
docker exec symfony_app vendor/bin/phpdoc
```
