<?php
function fx_transliterate($text) {

  $tr = array("А"=>"A", "а"=>"a", "Б"=>"B", "б"=>"b",
        "В"=>"V", "в"=>"v", "Г"=>"G", "г"=>"g",
        "Д"=>"D", "д"=>"d", "Е"=>"E", "е"=>"e",
        "Ё"=>"E", "ё"=>"e", "Ж"=>"Zh", "ж"=>"zh",
        "З"=>"Z", "з"=>"z", "И"=>"I", "и"=>"i",
        "Й"=>"Y", "й"=>"y", "КС"=>"X", "кс"=>"x",
        "К"=>"K", "к"=>"k", "Л"=>"L", "л"=>"l",
        "М"=>"M", "м"=>"m", "Н"=>"N", "н"=>"n",
        "О"=>"O", "о"=>"o", "П"=>"P", "п"=>"p",
        "Р"=>"R", "р"=>"r", "С"=>"S", "с"=>"s",
        "Т"=>"T", "т"=>"t", "У"=>"U", "у"=>"u",
        "Ф"=>"F", "ф"=>"f", "Х"=>"H", "х"=>"h",
        "Ц"=>"Ts", "ц"=>"ts", "Ч"=>"Ch", "ч"=>"ch",
        "Ш"=>"Sh", "ш"=>"sh", "Щ"=>"Sch", "щ"=>"sch",
        "Ы"=>"Y", "ы"=>"y", "Ь"=>"'", "ь"=>"'",
        "Э"=>"E", "э"=>"e", "Ъ"=>"'", "ъ"=>"'",
        "Ю"=>"Yu", "ю"=>"yu", "Я"=>"Ya", "я"=>"ya");

  $tr_text = strtr($text, $tr);

  return $tr_text;
}

define("FX_ADMINPANEL_PAGE", "Страница");
define("FX_ADMINPANEL_PAGE_VIEW", "Просмотр");
define("FX_ADMINPANEL_PAGE_EDIT", "Редактирование");
define("FX_ADMINPANEL_PAGE_DESIGNER", "Конструктор");
define("FX_ADMINPANEL_PAGE_MORE_STAT", "Статистика посещений");
define("FX_ADMINPANEL_PAGE_MORE_SEO", "SEO-анализ страницы");

define("FX_ADMINPANEL_SOMESITE", "Какойто сайт");
define("FX_ADMINPANEL_SITE", "Сайт");
define("FX_ADMINPANEL_SITE_SITEMAP", "Карта сайта");
define("FX_ADMINPANEL_SITE_SYSTEM", "Служебные разделы");
define("FX_ADMINPANEL_SITE_MAP", "Карта сайта");
define("FX_ADMINPANEL_SITE_MORE_SETTINGS", "Настройки");
define("FX_ADMINPANEL_SITE_DESIGN", "Дизайн");
define("FX_ADMINPANEL_SITE_STAT", "Статистика посещений");
define("FX_ADMINPANEL_SITE_SEO", "SEO-анализ сайта");
define("FX_ADMINPANEL_SITE_BUTTON_ADD", "Добавить");
define("FX_ADMINPANEL_SITE_BUTTON_EDIT", "Редактировать");
define("FX_ADMINPANEL_SITE_BUTTON_ON", "Включить");
define("FX_ADMINPANEL_SITE_BUTTON_OFF", "Выключить");
define("FX_ADMINPANEL_SITE_BUTTON_SETTINGS", "Настройки");
define("FX_ADMINPANEL_SITE_BUTTON_DELETE", "Удалить");
define("FX_ADMINPANEL_SITE_DESIGN_SETTINGS", "Настройки дизайна");
define("FX_ADMINPANEL_SITE_PAGE_SETTINGS", "Настройки страницы");
define("FX_ADMINPANEL_SITE_BUTTON_UNDO", "Назад");
define("FX_ADMINPANEL_SITE_BUTTON_REDO", "Вперёд");
define("FX_ADMINPANEL_SITE_TRASH", "Корзина");
define("FX_ADMINPANEL_SITE_TRASH_NEWS", "Новости");
define("FX_ADMINPANEL_SITE_TRASH_PHOTO", "Фотографии");
define("FX_ADMINPANEL_SITE_TRASH_SUB", "Разделы");
define("FX_ADMINPANEL_SITE_TRASH_USERS", "Пользователи");
define("FX_ADMINPANEL_SITE_MORE_ARCH", "Создать архив сайта");
define("FX_ADMINPANEL_SITE_MORE_NEW", "Новый сайт");

define("FX_ADMINPANEL_MODERATING", "Модерирование");
define("FX_ADMINPANEL_MODERATING_ALL", "Все");
define("FX_ADMINPANEL_MODERATING_ON", "Включенные");
define("FX_ADMINPANEL_MODERATING_OFF", "Выключенные");
define("FX_ADMINPANEL_MODERATING_DELETED", "Удаленные");
define("FX_ADMINPANEL_MODERATING_BUTTON_CALENDAR", "Календарь");
define("FX_ADMINPANEL_MODERATING_BUTTON_EDIT", "Редактировать");
define("FX_ADMINPANEL_MODERATING_BUTTON_ONOFF", "Включить/Выключить");
define("FX_ADMINPANEL_MODERATING_BUTTON_DELETE", "Удалить");

define("FX_ADMINPANEL_MORE", "ещё &darr;");

define("FX_ADMINPANEL_DESIGN", "Дизайн");
define("FX_ADMINPANEL_DESIGN_OTHER", "Выбрать другой");

define("FX_ADMINPANEL_DEVELOP", "Разработка");
define("FX_ADMINPANEL_DEVELOP_COMPONENT", "Компоненты");
define("FX_ADMINPANEL_DEVELOP_TEMPLATE", "Макеты");
define("FX_ADMINPANEL_DEVELOP_WIDGET", "Виджеты");
define("FX_ADMINPANEL_DEVELOP_OTHER", "Другие инструменты");
define("FX_ADMINPANEL_DEVELOP_OTHER_SQL", "SQL-консоль");


define("FX_ADMINPANEL_ADMIN", "Админ-панель");
define("FX_ADMINPANEL_ADMIN_USER", "Пользователи");
define("FX_ADMINPANEL_ADMIN_TOOLS", "Инструменты");
define("FX_ADMINPANEL_ADMIN_ADMIN", "Администрирование");
define("FX_ADMINPANEL_ADMIN_SETTINGS", "Настройки");


define("FX_ADMINPANEL_EXIT", "Выход");

define("FX_ADMINPANEL_VIEW_ADMIN_USER_USERS", "Пользователи");
define("FX_ADMINPANEL_VIEW_ADMIN_USER_USERS_ALL", "Все");
define("FX_ADMINPANEL_VIEW_ADMIN_USER_USERS_ON", "Включенные");
define("FX_ADMINPANEL_VIEW_ADMIN_USER_USERS_OFF", "Выключенные");
define("FX_ADMINPANEL_VIEW_ADMIN_USER_USERS_WAITING", "Ожидающие подтверждения");

define("FX_ADMINPANEL_VIEW_ADMIN_USER_GROUPS", "Группы");
define("FX_ADMINPANEL_VIEW_ADMIN_USER_GROUPS_EXTERNAL", "Внешние");
define("FX_ADMINPANEL_VIEW_ADMIN_USER_GROUPS_ADMINISTRATION", "Администрация");

define("FX_ADMINPANEL_VIEW_ADMIN_USER_ACCESS", "С правами");
define("FX_ADMINPANEL_VIEW_ADMIN_USER_ACCESS_SUPERVISOR", "Супервизор");
define("FX_ADMINPANEL_VIEW_ADMIN_USER_ACCESS_SITEADMIN", "Администратор сайта");
define("FX_ADMINPANEL_VIEW_ADMIN_USER_ACCESS_CATADMIN", "Администратор раздела");
define("FX_ADMINPANEL_VIEW_ADMIN_USER_ACCESS_GUEST", "Гость");

define("FX_ADMINPANEL_VIEW_ADMIN_TOOLS_SYSMESSAGE", "Системные сообщения");

define("FX_ADMINPANEL_VIEW_ADMIN_TOOLS_FILEMANAGER", "Файл-менеджер");

define("FX_ADMINPANEL_MANAGE", "Управление");
define("FX_ADMINPANEL_MANAGE_ADMIN", "Администрирование");
define("FX_ADMINPANEL_MANAGE_SETTINGS", "Настройки");
define("FX_ADMINPANEL_MANAGE_SETTINGS_MINISHOP", "Минимагазин");
define("FX_ADMINPANEL_MANAGE_SETTINGS_SYSTEM", "Системные");

define("FX_ADMINPANEL_MANAGE_USERS", "Пользователи");
define("FX_ADMINPANEL_MANAGE_USERS_ALL", "Все");
define("FX_ADMINPANEL_MANAGE_USERS_ON", "Включенные");
define("FX_ADMINPANEL_MANAGE_USERS_OFF", "Выключенные");
define("FX_ADMINPANEL_MANAGE_USERS_GROUP", "По группам");
define("FX_ADMINPANEL_MANAGE_USERS_SEARCH", "Поиск пользователей");
define("FX_ADMINPANEL_MANAGE_USERS_SUBSCRIBE", "Рассылка");
define("FX_ADMINPANEL_MANAGE_USERS_GROUPCONTROL", "Управление группами");

define("FX_ADMINPANEL_MANAGE_TOOLS", "Инструменты");
define("FX_ADMINPANEL_MANAGE_TOOLS_INTERACTIVE", "Интерактивные");
define("FX_ADMINPANEL_MANAGE_TOOLS_INTERACTIVE_FORUM", "Управление форумом");
define("FX_ADMINPANEL_MANAGE_TOOLS_SERVICE", "Сервисные");
define("FX_ADMINPANEL_MANAGE_TOOLS_SERVICE_SEARCH", "Поиск по сайту");
define("FX_ADMINPANEL_MANAGE_TOOLS_SERVICE_MINI", "Минимагазин: заказы");
define("FX_ADMINPANEL_MANAGE_TOOLS_SERVICE_REDIRECT", "Переадресации");
define("FX_ADMINPANEL_MANAGE_TOOLS_SERVICE_FILE", "Файл-менеджер");
define("FX_ADMINPANEL_MANAGE_TOOLS_SERVICE_CRON", "Управлени задачами");
define("FX_ADMINPANEL_MANAGE_TOOLS_REPORT", "Отчеты");
define("FX_ADMINPANEL_MANAGE_TOOLS_REPORT_STAT", "Статистика посещений");
define("FX_ADMINPANEL_MANAGE_TOOLS_REPORT_URL", "Битые ссылки");
define("FX_ADMINPANEL_MANAGE_TOOLS_REPORT_COMMENTS", "Комментарии посетителей");

define("FX_ADMINPANEL_MANAGE_ADMINISTRATE", "Администрирование");
define("FX_ADMINPANEL_MANAGE_ADMINISTRATE_SITE", "Сайты");
define("FX_ADMINPANEL_MANAGE_ADMINISTRATE_TEMPLATE", "Макеты дизайна");
define("FX_ADMINPANEL_MANAGE_ADMINISTRATE_MODULE", "Модули");
define("FX_ADMINPANEL_MANAGE_ADMINISTRATE_ARCH", "Архивирование проекта");
define("FX_ADMINPANEL_MANAGE_ADMINISTRATE_LIST", "Списки");
define("FX_ADMINPANEL_MANAGE_ADMINISTRATE_SETTINGS", "Настройки");
define("FX_ADMINPANEL_MANAGE_ADMINISTRATE_SETTINGS_SYS", "Системные");
define("FX_ADMINPANEL_MANAGE_ADMINISTRATE_SETTINGS_SEARCH", "Поиск по сайту");
define("FX_ADMINPANEL_MANAGE_ADMINISTRATE_SETTINGS_MINI", "Минимагазин");


// HISTORY
define("FX_HISTORY_INFOBLOCK_ADD", "Добавление инфоблока");
define("FX_HISTORY_INFOBLOCK_MOVE", "Перемещение инфоблоков");
define("FX_HISTORY_INFOBLOCK_EDIT", "Изменение инфоблока");
define("FX_HISTORY_INFOBLOCK_ON", "Включение инфоблока");
define("FX_HISTORY_INFOBLOCK_OFF", "Выключение инфоблока");
define("FX_HISTORY_INFOBLOCK_DELETE", "Удаление инфоблока");
define("FX_HISTORY_SUBDIVISION_ADD", "Добавление раздела");
define("FX_HISTORY_SUBDIVISION_MOVE", "Перемещение разделов");
define("FX_HISTORY_SUBDIVISION_EDIT", "Изменение раздела");
define("FX_HISTORY_SUBDIVISION_ON", "Включение раздела");
define("FX_HISTORY_SUBDIVISION_OFF", "Выключение раздела");
define("FX_HISTORY_SUBDIVISION_DELETE", "Удаление раздела");
define("FX_HISTORY_MESSAGE_ADD", "Добавление объекта");
define("FX_HISTORY_MESSAGE_MOVE", "Перемещение объектов");
define("FX_HISTORY_MESSAGE_EDIT", "Изменение объекта");
define("FX_HISTORY_MESSAGE_ON", "Включение объекта");
define("FX_HISTORY_MESSAGE_OFF", "Выключение объекта");
define("FX_HISTORY_MESSAGE_DELETE", "Удаление объекта");

// ACTIONS
define("FX_ACTION_INDEX", "просмотр");
define("FX_ACTION_ADD", "добавление");
define("FX_ACTION_SEARCH", "поиск");

// SORT
define("FX_ADMIN_SORT", "Сортировка");
define("FX_ADMIN_SORT_MANUAL", "вручную");
define("FX_ADMIN_SORT_LAST", "последние сверху");
define("FX_ADMIN_SORT_FIELD", "по полям");
define("FX_ADMIN_SORT_RANDOM", "случайно");
define("FX_ADMIN_SORT_ASC", "по возрастанию");
define("FX_ADMIN_SORT_DESC", "по убыванию");
define("FX_ADMIN_SORT_BY", "поле");
define("FX_ADMIN_SORT_ORDER", "порядок");


// ADMIN INTERFACE
define("FX_ADMIN_COMPONENT", "Компонент");
define("FX_ADMIN_COMPONENTS", "Контент-блоки");
define("FX_ADMIN_WIDGET", "Виджет");
define("FX_ADMIN_WIDGETS", "Виджеты");
define("FX_ADMIN_KEYWORD", "Ключевое слово");
define("FX_ADMIN_DEFAULT_ACTION", "Действие по умолчанию");
define("FX_ADMIN_DEFAULT_VALUE", "Значение по умолчанию");
define("FX_ADMIN_REC_NUM", "Количество выводимых записей");

define("FX_ADMIN_LABEL", "Заголовок");
define("FX_ADMIN_NAME", "Имя");
define("FX_ADMIN_TYPE", "Тип");

// типы полей
define("FX_ADMIN_FIELD_STRING","Строка");
define("FX_ADMIN_FIELD_INT","Целое число");
define("FX_ADMIN_FIELD_TEXT","Текст");
define("FX_ADMIN_FIELD_SELECT","Список");
define("FX_ADMIN_FIELD_BOOL","Логическая переменная");
define("FX_ADMIN_FIELD_FILE","Файл");
define("FX_ADMIN_FIELD_FLOAT","Дробное число");
define("FX_ADMIN_FIELD_DATETIME","Дата и время");
define("FX_ADMIN_FIELD_COLOR","Цвет");
define("FX_ADMIN_FIELD_INFOBLOCK","Инфоблок");
define("FX_ADMIN_FIELD_IMAGE","Изображение");
define("FX_ADMIN_FIELD_MULTISELECT","Мультисписок");
define("FX_ADMIN_FIELD_LINK","Связь с другим объектом");


define("FX_ADMIN_LAYER_MORE_SHOW", "показать дополнительные настройки");
define("FX_ADMIN_LAYER_MORE_HIDE", "скрыть дополнительные настройки");

define("FX_ADMIN_INFOBLOCK_TITLE", "Заголовок");
define("FX_ADMIN_INFOBLOCK_CTPL", "Шаблон");
define("FX_ADMIN_INFOBLOCK_CONTENT", "Контент");
define("FX_ADMIN_INFOBLOCK_CONTENT_OWN", "сообственный");
define("FX_ADMIN_INFOBLOCK_CONTENT_FROM", "из другой страницы");
define("FX_ADMIN_INFOBLOCK_CONTENT_SOURCE", "Источник");
define("FX_ADMIN_INFOBLOCK_CONTENT_NOT_SOURCE", "Нету таких источников. Может создать его?");

define("FX_ADMIN_OBJECT_REMOVED", "Объект удален");
define("FX_ADMIN_OBJECTS_REMOVED", "Объекты удалены");

define("FX_ADMIN_PERMISSION_1", "Директор");

define("FX_FRONT_LISTS_CHOOSE", '-- выбрать --');
define("FX_FRONT_FIELD_FILED", "Поле \"%s\" является обязательным для заполнения.");
define("FX_FRONT_FIELD_INT_ENTER_INTEGER", "В поле \"%s\" необходимо ввести целое число.");

// BBCODE
define("FX_BBCODE_SIZE", "Размер шрифта");
define("FX_BBCODE_COLOR", "Цвет шрифта");
define("FX_BBCODE_SMILE", "Смайлы");
define("FX_BBCODE_B", "Жирный");
define("FX_BBCODE_I", "Курсив");
define("FX_BBCODE_U", "Подчёркнутый");
define("FX_BBCODE_S", "Зачёркнутый");
define("FX_BBCODE_LIST", "Элемент списка");
define("FX_BBCODE_QUOTE", "Цитата");
define("FX_BBCODE_CODE", "Код");
define("FX_BBCODE_IMG", "Изображение");
define("FX_BBCODE_URL", "Ссылка");
define("FX_BBCODE_CUT", "Сократить текст");

define("FX_BBCODE_QUOTE_USER", "писал(а)");
define("FX_BBCODE_CUT_MORE", "подробнее");
define("FX_BBCODE_SIZE_DEF", "размер");
define("FX_BBCODE_ERROR_1", "введён BB-код недопустимого формата:");
define("FX_BBCODE_ERROR_2", "введены BB-коды недопустимого формата:");


define("FX_ADMIN_RIGHTS_INHERIT", "наследовать");
define("FX_ADMIN_RIGHTS_ALL", "все");
define("FX_ADMIN_RIGHTS_REG", "зарегистрированные");
define("FX_ADMIN_RIGHTS_AUTH", "уполномоченные");
define("FX_ADMIN_RIGHTS_READ", "просмотр");
define("FX_ADMIN_RIGHTS_ADD", "добавление");
define("FX_ADMIN_RIGHTS_EDIT", "изменение");
define("FX_ADMIN_RIGHTS_CHECKED", "включение/выключение");
define("FX_ADMIN_RIGHTS_DELETE", "удаление");

define("PIE_EDIT IMAGE","Редактировать изображение");
define("PIE_RESIZE IMAGE","Изменение размера");
define("PIE_WIDTH","Ширина");
define("PIE_HEIGHT","Высота");
define("PIE_KEEP PROPORTIONS","Сохранить пропорции");
define("PIE_ROTATE IMAGE","Повернуть изображение");
define("PIE_LEFT 90 DEGREES","Повернуть на 90° против часовой стрелки");
define("PIE_RIGHT 90 DEGREES","Повернуть на 90° по часовой стрелке");
define("PIE_CROP IMAGE","Обрезать изображение");
define("PIE_EFFECTS","Эффекты");
define("PIE_SAVE AND CLOSE","Сохранить и закрыть");
define("PIE_SAVE AS","Сохранить как");
define("PIE_GRAYSCALE","В оттенках серого");
define("PIE_CONTRAST","Контраст");
define("PIE_BRIGHTNESS","Яркость");
define("PIE_DARKER","Темнее");
define("PIE_IS REQUIRED","требуется");
define("PIE_MUST BE NUMERIC","должно быть числовым значением");
define("PIE_NOT NEGATIVE","должно быть положительным числом");
define("PIE_NOT IN RANGE","значение находится вне диапазона");
define("PIE_CANT BE LARGER THEN","не может быть больше, чем");
define("PIE_NO PROVIDED IMAGE","Изображение не было выбрано.");
define("PIE_IMAGE DOES NOT EXIST","Изображение не существует.");
define("PIE_INVALID IMAGE TYPE","Поддерживаются только файлы форматов jpeg, png или gif.");
define("PIE_OLD PHP VERSION","слишком старая версия PHP. Минимальные требования:");
define("PIE_OLD GD VERSION","слишком старая версия библиотеки GD. Минимальные требования:");
define("PIE_UNDO","Отменить");
define("PIE_UPDATE","Обновить");
define("PIE_CROP","Обрезать");
define("PIE_LOADING","Подождите, идет обработка");
define("PIE_AN UNEXPECTED ERROR","Непредвиденная ошибка, попробуйте еще раз...");
define("PIE_RESIZE HELP","Измените значения ширины и высоты и нажмите кнопку");
define("PIE_CROP HELP","Нажмите и тяните для выделения участка изображения для кадрирования.");
define("PIE_CROP HELP FIELDS","Или используйте поля для обрезки.");
define("PIE_CROP WIDTH","Ширина кадрирования");
define("PIE_CROP HEIGHT","Высота кадрирования");
define("PIE_CROP KEEP PROPORTIONS","Сохранить пропорции кадрирования");
define("PIE_INSTRUCTIONS","Подсказка");
define("PIE_TEXT","Текст");
define("PIE_FONT","Шрифт");
define("PIE_FONTCOLOR","Цвет шрифта");
define("PIE_FONTSIZE","Размер шрифта");
define("PIE_ROTATE TEXT","Повернуть текст");
define("PIE_START POSITION X","Начальное положение по оси X");
define("PIE_START POSITION Y","Начальное положение по оси Y");
define("PIE_SELECT A NEW OR EXISTING TEXT","Выберите существующий или новый текст");
define("PIE_NEW TEXT","Новый текст");
define("PIE_TEXT HELP","Введите текст и нажмите \"Обновить\".<br/>Начальное положение по осям X и Y отсчитывается от верхнего левого угла изображения.<br/>Вы можете нажать на изображение для выбора начальной позиции.");
define("PIE_DELETE TEXT","Удалить текст");
define("PIE_ROUNDED CORNERS","Закругленные углы");
define("PIE_SIZE RADIUS","Размер / радиус");
define("PIE_BACKGROUND COLOR","Цвет фона");
define("PIE_RADIUS TOO LARGE","Size/radius can´t be higher than half the size of the smallest side:");
define("PIE_RADIUS TOO SMALL","Размер / радиус не может быть меньше единицы.");
define("PIE_UPGRADE AVAILABLE","Обновление доступно (версия");
define("PIE_COLORPICKER SUBMIT","OK");
define("PIE_RESET","Сброс");
define("PIE_FILENAME ERROR","Имя файла может содержать только с a по z, - и _.");
define("PIE_IMAGE QUALITY","Качество изображения");
define("PIE_FLIP HORIZONTAL","Отразить слева направо");
define("PIE_FLIP VERTICAL","Переворот вертикальный");
?>