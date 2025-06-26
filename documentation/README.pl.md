# Gazeta Internetowa

## Wymagania

- **PHP:** 8.1 lub wyższy
- **Composer:** Najnowsza wersja
- **MySQL:** 8.0 lub wyższy
- **Docker & Docker Compose**

## Instalacja

### Krok 1: Sklonuj Repozytorium
```bash
git clone <url-repozytorium>
cd gazeta-internetowa
```

### Krok 2: Zainstaluj Zależności
```bash
composer install
```

### Krok 3: Konfiguracja Bazy Danych
```bash
# Utwórz bazę danych
php bin/console doctrine:database:create

# Uruchom migracje
php bin/console doctrine:migrations:migrate

# Załaduj dane testowe
php bin/console doctrine:fixtures:load --no-interaction
```

### Krok 4: Uruchom Aplikację
```bash
# Używając Symfony CLI
symfony server:start

# Lub używając wbudowanego serwera PHP
php -S localhost:8000 -t public/
```

## Uruchamianie Testów

```bash
# Uruchom wszystkie testy
php vendor/bin/phpunit

# Uruchom testy z pokryciem kodu
XDEBUG_MODE=coverage php vendor/bin/phpunit --coverage-html coverage/

# Uruchom testy z szczegółowym wyjściem
php vendor/bin/phpunit --testdox
```

## Generowanie Dokumentacji

```bash
# Zainstaluj phpDocumentor (jeśli potrzebne)
curl -L -o phpDocumentor.phar https://github.com/phpDocumentor/phpDocumentor/releases/download/v3.4.3/phpDocumentor.phar
chmod +x phpDocumentor.phar

# Wygeneruj dokumentację API
php phpDocumentor.phar run -d src -t docs/

# Wyczyść
rm phpDocumentor.phar
```

## Jakość Kodu

```bash
# Uruchom sprawdzenie jakości kodu
./check_code_ztp2.sh

# Napraw problemy ze stylem kodu
./vendor/bin/phpcbf src/

# Sprawdź styl kodu
./vendor/bin/phpcs src/
```

## Domyślni Użytkownicy

Po załadowaniu fixtures, dostępni są następujący użytkownicy:

### Administrator
- **Email:** `admin@example.com`
- **Hasło:** `admin123`
- **Rola:** `ROLE_ADMIN`

### Zwykli Użytkownicy
- Wiele użytkowników testowych z emailami jak `user1@example.com`, `user2@example.com`
- **Hasło:** `password123`

## Struktura Projektu

```
gazeta-internetowa/
├── src/
│   ├── Command/          # Komendy konsolowe
│   ├── Controller/       # Kontrolery web
│   ├── Entity/          # Encje bazy danych
│   ├── Form/            # Typy formularzy
│   ├── Repository/      # Warstwa dostępu do danych
│   ├── Security/        # Uwierzytelnianie i autoryzacja
│   └── Service/         # Logika biznesowa
├── templates/           # Szablony Twig
├── tests/              # Testy jednostkowe i funkcjonalne
├── config/             # Konfiguracja aplikacji
├── migrations/         # Migracje bazy danych
├── documentation/      # Wygenerowana dokumentacja
└── public/            # Katalog główny web
```

## Funkcjonalności

- **Zarządzanie Użytkownikami:** Rejestracja, logowanie, zarządzanie profilem
- **System Artykułów:** Tworzenie, edycja, usuwanie artykułów z kategoriami
- **Komentowanie:** Użytkownicy mogą komentować artykuły
- **Panel Administracyjny:** Zarządzanie użytkownikami, moderacja treści
- **Wielojęzyczność:** Tłumaczenia angielskie i polskie
- **Responsywny Design:** Interfejs przyjazny dla urządzeń mobilnych

## Pokrycie Testami

- **Łącznie Testów:** 171
- **Asercji:** 485
- **Pokrycie:** 69.22% ogólnie
- **Status:** Wszystkie testy przechodzą ✅

## Rozwój

### Migracje Bazy Danych
```bash
# Utwórz nową migrację
php bin/console make:migration

# Uruchom migracje
php bin/console doctrine:migrations:migrate

# Zresetuj bazę danych
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

### Dodawanie Nowych Funkcjonalności
1. Utwórz encję: `php bin/console make:entity`
2. Utwórz migrację: `php bin/console make:migration`
3. Utwórz kontroler: `php bin/console make:controller`
4. Utwórz formularz: `php bin/console make:form`
5. Napisz testy w katalogu `tests/`

## Wdrożenie

### Wdrożenie Produkcyjne
1. Ustaw `APP_ENV=prod` w środowisku
2. Wyczyść cache: `php bin/console cache:clear --env=prod`
3. Uruchom migracje: `php bin/console doctrine:migrations:migrate --env=prod`
4. Załaduj fixtures: `php bin/console doctrine:fixtures:load --env=prod`

## Dokumentacja

Pełna dokumentacja dostępna w katalogu `documentation/`:
- **Dokumentacja API:** `documentation/api-docs/index.html`
- **Wyniki Testów:** `documentation/test-results.txt`
- **Raport Jakości Kodu:** `documentation/code-quality-report.txt`

---

**Stworzone przez:** Konrad Stomski  
**Kurs:** ZTP2 
**Data:** Czerwiec 2025 