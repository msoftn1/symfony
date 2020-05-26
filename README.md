**Инструкция**

1. установить композер
2. запустить "composer update"
3. настроить базу в .env
4. запустить "php bin/console doctrine:schema:update --force"
5. настроить сервер на директорию public/

Команды импорта:   
php bin/console app:import-categories json/categories.json  
php bin/console app:import-products json/products.json      

Где json/categories.json и json/products.json ваши файлы.