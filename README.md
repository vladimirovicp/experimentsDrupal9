# experimentsDrupal

## Setting
> * php: "8.0"
> * mariadb: "10.3"
> * запрет обновления settings.ddev.php (disable_settings_management: true)

## ddev comands
> * **Экспорт:** ddev export-db --file=./private/db/experimentsDrupal.sql.gz
> * **Импорт:** ddev import-db --src=./private/db/experimentsDrupal.sql.gz

## ddev drush comands
> * **Чистка кэша:** ddev drush cr
> * **Экспорт всех настроек:** ddev drush cex -y
> * **Откат конфигурации:** ddev drush cim -y
> * **Экспорт переводов:** ddev drush locale:export en > config/locale/en.po && ddev drush locale:export ru > config/locale/ru.po
> * **Импорт переводов:**  ddev drush locale:import en > config/locale/en.po && ddev drush locale:import ru > config/locale/ru.po


## Стандартные модули
> * Configuration Translation
> * Content Translation
> * Interface Translation
> * Language
>
> * Media
> * Media Library
>
> ### Модули отвечающие за Cache
> * Internal Page Cache
> * Internal Dynamic Page Cache

## Модули
> * admin_toolbar
> * devel
> * token
> * pathauto
> * ctools
