
## Быстрый старт

### Клонирование и установка зависимостей

```bash
git clone <repository-url>
cd <project-folder>
```

Можно выполнить полную настройку одной командой:

```bash
make setup
```

Эта команда автоматически:
- установит PHP-зависимости (`composer install`)
- создаст `.env` из `.env.example`
- сгенерирует `APP_KEY`
- запустит миграции
- создаст индекс в Elasticsearch


### Заполнение тестовыми данными (100000 товаров)

```bash
./vendor/bin/sail artisan db:seed
```

Команда создаёт категории и **5000 тестовых товаров** с русскоязычными названиями.

### Индексация в Elasticsearch

```bash
./vendor/bin/sail artisan search:reindex-products
```

Команда создаёт индекс `products` в Elasticsearch и импортирует туда все товары из БД.

### Запуск в режиме разработки

#### Вариант A — через Laravel Sail (Docker)

```bash
./vendor/bin/sail up -d
```

Приложение будет доступно по адресу: [http://localhost](http://localhost)

Для остановки:

```bash
./vendor/bin/sail down
```

## Docker-инфраструктура (Laravel Sail)

При запуске через Sail поднимаются следующие сервисы:

| Сервис | Порт (хост) | Описание |
|--------|-------------|----------|
| App | `80` | Laravel приложение |
| MySQL | `3307` | Основная БД |
| MariaDB | `3308` | Альтернативная БД |
| Redis | `6379` | Кэш / очереди |
| Meilisearch | `7700` | Поисковый движок |
| Elasticsearch | `9200` | Поисковый движок |


## Пример HTTP-запроса к API продуктов.
Используется для ручного тестирования эндпоинта /api/products с фильтрами.

Откройте этот [файл](.http/products.http) в PhpStorm и выполните запрос.
### GET request with parameter

GET http://localhost/api/products?q=Робот-пылесос&price_to=50000&price_from=2000&page=1&in_stock=1

Accept: application/json

