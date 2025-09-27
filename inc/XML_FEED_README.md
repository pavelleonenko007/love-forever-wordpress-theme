# XML Feed Generator для Love Forever

## Описание

Функционал для генерации XML фидов товаров в формате YML каталога для интеграции с внешними системами (например, Яндекс.Маркет).

## Структура файлов

- `class-xml-feed-generator.php` - Основной класс для генерации XML фидов
- `class-wp-cli-xml-feed.php` - WP-CLI команды для управления фидами
- `xml-feed-cron.php` - Standalone скрипт для запуска через cron
- `xml-feed-loader.php` - Загрузчик функционала и админ-интерфейс
- `test-xml-feed.php` - Тестовый скрипт

## Возможности

### 1. Генерация XML фидов
- Генерация фида для конкретной категории
- Генерация фидов для всех категорий
- Автоматическое создание директорий
- Сохранение в uploads и публичную директорию

### 2. WP-CLI команды
```bash
# Генерация фида для конкретной категории
wp xml-feed generate wedding

# Генерация фидов для всех категорий
wp xml-feed generate-all

# Список доступных категорий
wp xml-feed list-categories

# Очистка сгенерированных файлов
wp xml-feed clean

# Статус сгенерированных файлов
wp xml-feed status
```

### 3. Standalone скрипт для cron
```bash
# Генерация фида для конкретной категории
php xml-feed-cron.php wedding

# Генерация фидов для всех категорий
php xml-feed-cron.php --all
```

### 4. Админ-интерфейс
- Доступен в админке WordPress: Платья → XML Feeds
- Генерация фидов через веб-интерфейс
- Просмотр статуса файлов
- Очистка файлов

## Структура XML

Генерируемый XML соответствует формату YML каталога:

```xml
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="2025-01-23 12:00">
  <shop>
    <name>Salon Love Forever</name>
    <company>Salon Love Forever</company>
    <url>https://salon-love.ru</url>
    <platform>WordPress</platform>
    <version>1.0</version>
    <currencies>
      <currency id="RUR" rate="1"/>
    </currencies>
    <categories>
      <category id="1">Свадебные платья</category>
    </categories>
    <offers>
      <offer id="123" available="true">
        <name>Название платья</name>
        <collectionId>wedding</collectionId>
        <url>https://salon-love.ru/wedding/dress-name/</url>
        <price>50000</price>
        <currencyId>RUR</currencyId>
        <categoryId>1</categoryId>
        <picture>https://salon-love.ru/image1.jpg</picture>
        <picture>https://salon-love.ru/image2.jpg</picture>
        <store>true</store>
      </offer>
    </offers>
    <collections>
      <collection id="wedding">
        <url>https://salon-love.ru/wedding</url>
        <name>Свадебные платья в магазине Love Forever</name>
        <description>Описание категории</description>
        <picture>https://salon-love.ru/category-image.jpg</picture>
      </collection>
    </collections>
  </shop>
</yml_catalog>
```

## Настройка cron

### Системный cron
Добавьте в crontab:

```bash
# Генерация всех фидов ежедневно в 2:00
0 2 * * * php /path/to/wordpress/wp-content/themes/loveforever/inc/xml-feed-cron.php --all

# Генерация фида для свадебных платьев каждые 6 часов
0 */6 * * * php /path/to/wordpress/wp-content/themes/loveforever/inc/xml-feed-cron.php wedding
```

### WordPress cron
```php
// Добавить в functions.php или плагин
if ( ! wp_next_scheduled( 'generate_xml_feeds' ) ) {
    wp_schedule_event( time(), 'daily', 'generate_xml_feeds' );
}

add_action( 'generate_xml_feeds', function() {
    $generator = XML_Feed_Generator::get_instance();
    $generator->generate_all_feeds();
});
```

## Расположение файлов

XML файлы сохраняются в двух местах:

1. **Uploads директория**: `/wp-content/uploads/xml/category-slug.xml`
2. **Публичная директория**: `/xml/category-slug.xml` (доступна по URL)

## Требования

- WordPress 5.0+
- PHP 7.4+
- ACF (Advanced Custom Fields) для работы с полями товаров
- Custom Post Type `dress`
- Taxonomy `dress_category`

## Поля товаров

Функционал использует следующие поля ACF:

- `price` - основная цена
- `price_with_discount` - цена со скидкой
- `has_discount` - есть ли скидка
- `availability` - доступность товара
- `images` - галерея изображений
- `description` - описание товара

## Логирование

Standalone скрипт создает лог файл:
- `/wp-content/xml-feed-cron.log`

## Безопасность

- Все AJAX запросы защищены nonce
- Проверка прав доступа для админ-функций
- Экранирование всех выходных данных
- Валидация входных параметров

## Производительность

- Использование WP_Query с оптимизированными параметрами
- Кэширование результатов запросов
- Пакетная обработка товаров
- Минимальное использование памяти

## Отладка

Для отладки используйте:

```php
// Включить отладку WordPress
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);

// Проверить доступные категории
$generator = XML_Feed_Generator::get_instance();
$categories = $generator->get_available_categories();
var_dump($categories);
```

## Поддержка

При возникновении проблем:

1. Проверьте логи WordPress (`/wp-content/debug.log`)
2. Проверьте логи cron (`/wp-content/xml-feed-cron.log`)
3. Убедитесь, что все необходимые плагины активны
4. Проверьте права доступа к директориям
