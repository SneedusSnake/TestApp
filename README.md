# TestApp

## Инструкция по развёртыванию проекта

### Предварительные требования:
Для запуска требуется установленый docker-compose, а так-же свободный порт 3306 для MySQL контейнера.

### Установка
1. Склонируйте репозиторий: `git clone git@github.com:SneedusSnake/TestApp.git`
2. Перейдите в корень проекта `cd ./TestApp`
3. Запустите скрипт установки командой `make install`
4. После завершения работы скрипта вебсайт будет доступен по адресу http://localhost/

### Наполнение данными
Для того, чтобы заполнить базу тестовыми записями запустите команду `make seed`
