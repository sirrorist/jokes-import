# Amo Point - тестовое Laravel-приложение

Laravel 13 + PHP 8.4. Решение включает импорт шуток по расписанию, REST API, аналитику посещений и встраиваемые JS-скрипты.

## Требования

- PHP 8.4+
- Composer 2
- Node.js 20+ (для сборки фронтенда Breeze)
- SQLite (по умолчанию) или MySQL/PostgreSQL

## Установка

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # если используете SQLite
php artisan migrate
php artisan db:seed
npm install && npm run build
```

### Демо-пользователь (после `db:seed`)

- Email: `admin@example.com`
- Password: `password`

## Планировщик (импорт шуток каждые 5 минут)

Команда:

```bash
php artisan jokes:import
```

Cron (на сервере):

```cron
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Локально:

```bash
php artisan schedule:work
```

Логи импорта: `storage/logs/jokes-import.log`.

## API

| Метод | URL | Описание |
|-------|-----|----------|
| GET | `/api/jokes` | Список импортированных записей (`per_page`, `page`, `limit`, `offset`, `source`) |
| POST | `/api/analytics/visit` | Приём события визита |

Swagger UI: `/api/documentation` (после `php artisan l5-swagger:generate`).

Пример:

```bash
curl http://localhost:8000/api/jokes
curl -X POST http://localhost:8000/api/analytics/visit \
  -H "Content-Type: application/json" \
  -d '{"url":"https://example.com","visitor_id":"test-1","device_type":"desktop"}'
```

## Статистика

- URL: `/analytics` (требуется вход через Breeze)
- График: уникальные визиты по часам (Chart.js)
- Круговая диаграмма: города

## JS для testlist.html

Подключение на странице [testlist](http://test.amopoint-dev.ru/testzz/testlist.html):

```html
<script src="https://YOUR-DOMAIN/js/testlist-fields.js" defer></script>
```

Алгоритм: при смене `<select>` показываются только поля, у которых `name` содержит выбранное значение.

## Счётчик посещений (внешний сайт)

```html
<script
  src="https://YOUR-DOMAIN/js/amo-tracker.js"
  data-endpoint="https://YOUR-DOMAIN/api/analytics/visit"
  defer
></script>
```

**Почему IP/город не на клиенте:** браузер не отдаёт реальный IP (NAT, VPN, политика приватности); геолокация по IP возможна только на сервере. Скрипт отправляет URL, user agent (неявно), тип устройства и `visitor_id` из `localStorage`; сервер определяет IP из запроса и город через [ip-api.com](http://ip-api.com).

## Тесты

```bash
php artisan test --compact
```

## Запуск локально

```bash
php artisan serve
# или
composer run dev
```

## Допущения

- Источник шуток: [official-joke-api](https://official-joke-api.appspot.com/random_joke) (бесплатный JSON без авторизации).
- Уникальный визит = один `visitor_id` не чаще одного раза в час (поле `is_unique_in_hour`).
- Геолокация: бесплатный ip-api.com (лимиты на проде).
- Для cross-origin трекера используется `visitor_id` в `localStorage`, а не cookie (SameSite ограничения).

## Структура

```
app/
  Console/Commands/ImportJokesCommand.php
  Data/                    # DTO
  Http/Controllers/Api/
  Http/Requests/
  Http/Resources/
  Models/
  Services/Jokes/
  Services/Analytics/
public/js/
  testlist-fields.js
  amo-tracker.js
routes/api.php
```
