<?php

namespace App\Main;

class DateTimeHelper
{

    // Get day of week by number
    public static function getDayOfWeek(int $dayNum, string $lang = 'ru', int $wordLength = 0): string
    {
        $arData = [
            'en' => [
                ['Sun', 'Sunday'], ['Mon', 'Monday'], ['Tue', 'Tuesday'],
                ['Wed', 'Wednesday'], ['Thur', 'Thursday'], ['Fri', 'Friday'],
                ['Sat', 'Saturday'],
            ],
            'ru' => [
                ['Вс', 'Воскресенье'], ['Пн', 'Понедельник'], ['Вт', 'Вторник'],
                ['Ср', 'Среда'], ['Чт', 'Четверг'], ['Пт', 'Пятница'],
                ['Сб', 'Суббота'],
            ],
        ];
        return (string)$arData[$lang][$dayNum][$wordLength];
    }

    // Get month name by number
    public static function getMonthName(int $monthNum, $arOptions = []): string
    {
        if (!isset($arOptions['lang'])) {
            $arOptions['lang'] = 'ru';
        }
        if (!isset($arOptions['format'])) {
            $arOptions['format'] = 1;
        }
        $arData = [
            'en' => [
                ['Jan', 'January'],
                ['Feb', 'February'],
                ['Mar', 'March'],
                ['Apr', 'April'],
                ['May', 'May'],
                ['Jun', 'June'],
                ['Jul', 'July'],
                ['Aug', 'August'],
                ['Sep', 'September'],
                ['Oct', 'October'],
                ['Nov', 'November'],
                ['Dec', 'December'],
            ],
            'ru' => [
                ['Янв', 'Январь', 'Января', 'Январе'],
                ['Фев', 'Февраль', 'Февраля', 'Феврале'],
                ['Мар', 'Март', 'Марта', 'Марте'],
                ['Апр', 'Апрель', 'Апреля', 'Апреле'],
                ['Май', 'Май', 'Мая', 'Мае'],
                ['Июн', 'Июнь', 'Июня', 'Июне'],
                ['Июл', 'Июль', 'Июля', 'Июле'],
                ['Авг', 'Август', 'Августа', 'Августе'],
                ['Сен', 'Сентябрь', 'Сентября', 'Сентябре'],
                ['Окт', 'Октябрь', 'Октября', 'Октябре'],
                ['Ноя', 'Ноябрь', 'Ноября', 'Ноябре'],
                ['Дек', 'Декабрь', 'Декабря', 'Декабре'],
            ],
        ];
        return $arData[$arOptions['lang']][$monthNum][$arOptions['format']];
    }

}