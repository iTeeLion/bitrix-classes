<?php


namespace App\Main;


class DateTimeHelper
{

    // get day of week by number
    public static function getDayOfWeek($d, $l = 'ru', $s = 0): string
    {
        $a = array(
            'en' => array(
                array('Sun', 'Sunday'), array('Mon', 'Monday'), array('Tue', 'Tuesday'),
                array('Wed', 'Wednesday'), array('Thur', 'Thursday'), array('Fri', 'Friday'),
                array('Sat', 'Saturday'),
            ),
            'ru' => array(
                array('Вс', 'Воскресенье'), array('Пн', 'Понедельник'), array('Вт', 'Вторник'),
                array('Ср', 'Среда'), array('Чт', 'Четверг'), array('Пт', 'Пятница'),
                array('Сб', 'Суббота'),
            ),
        );
        return $a[$l][$d][$s];
    }

    // get month name by number
    public static function getMonthName($m, $p = []): string
    {
        if (!isset($p['lang'])) {
            $p['lang'] = 'ru';
        }
        if (!isset($p['form'])) {
            $p['form'] = 1;
        }
        $a = array(
            'en' => array(
                array('Jan', 'January'),
                array('Feb', 'February'),
                array('Mar', 'March'),
                array('Apr', 'April'),
                array('May', 'May'),
                array('Jun', 'June'),
                array('Jul', 'July'),
                array('Aug', 'August'),
                array('Sep', 'September'),
                array('Oct', 'October'),
                array('Nov', 'November'),
                array('Dec', 'December'),
            ),
            'ru' => array(
                array('Янв', 'Январь', 'Января', 'Январе'),
                array('Фев', 'Февраль', 'Февраля', 'Феврале'),
                array('Мар', 'Март', 'Марта', 'Марте'),
                array('Апр', 'Апрель', 'Апреля', 'Апреле'),
                array('Май', 'Май', 'Мая', 'Мае'),
                array('Июн', 'Июнь', 'Июня', 'Июне'),
                array('Июл', 'Июль', 'Июля', 'Июле'),
                array('Авг', 'Август', 'Августа', 'Августе'),
                array('Сен', 'Сентябрь', 'Сентября', 'Сентябре'),
                array('Окт', 'Октябрь', 'Октября', 'Октябре'),
                array('Ноя', 'Ноябрь', 'Ноября', 'Ноябре'),
                array('Дек', 'Декабрь', 'Декабря', 'Декабре'),
            ),
        );
        return $a[$p['lang']][$m][$p['form']];
    }

}