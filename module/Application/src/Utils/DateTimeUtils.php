<?php /** @noinspection PhpUnused */

    /**
     * DateUtils.php
     *
     * @since 01.09.2017
     * @author Alexej Kisselev <alexej.kisselev@gmail.com>
     * @source https://github.com/nette/utils/blob/master/src/Utils/DateTime.php
     */

    namespace Application\Utils;

    use DateTime;
    use DateTimeZone;
    use Exception;

    class DateTimeUtils
    {
        public const DATEFORMAT_PHP = 'd.m.Y H:i:s';
        public const DATEFORMAT_JS = 'DD.MM.YYYY HH:mm:ss';

        /** minute in seconds */
        public const MINUTE = 60;

        /** hour in seconds */
        public const HOUR = 3600;

        /** day in seconds */
        public const DAY = 86400;

        /** week in seconds */
        public const WEEK = 604800;

        /** average month in seconds */
        public const MONTH = 2629800;

        /** average year in seconds */
        public const YEAR = 31557600;

        /**
         * DateTime object factory.
         * @param null $time
         * @param bool $micro
         * @return DateTime
         */
        public static function from($time = null, $micro = false): DateTime
        {
            if ($time instanceof DateTime) {
                return $time;
            }
            try {
                if (is_numeric($time)) {
                    if ($time <= self::YEAR) {
                        $time += time();
                    }
                    return (new DateTime('@' . $time / ($micro ? 1000 : 1)))->setTimeZone(new DateTimeZone(date_default_timezone_get()));
                }
                return new DateTime($time);
            } catch (Exception $e) {
            }
            return date_create($time);
        }

        /**
         * @param null $time
         * @param bool $micro
         * @return int|string
         */
        public static function getTimestamp($time = null, $micro = false)
        {
            $from = self::from($time);
            $ts = $from->format('U') * ($micro ? 1000 : 1);
            return is_float($tmp = $ts * 1) ? $ts : $tmp;
        }

        /**
         * @param false $micro
         * @return float|int|string
         */
        public static function getTimestampDefaultFrom($micro = false)
        {
            try {
                return self::getTimestamp(self::from()->setTime(0, 0, 0), $micro);
            } catch (Exception $e) {
            }
            return 0;
        }

        /**
         * gets now timestamp
         * @param bool $micro
         * @return int
         */
        public static function getTimestampDefaultTo($micro = false)
        {
            return self::getTimestamp(null, $micro);
        }

        /**
         * Gets formatted yesterday
         * @return string
         */
        public static function getDateDefaultFrom(): string
        {
            return self::from()->setTime(0, 0, 0)->format(self::DATEFORMAT_PHP);
        }

        /**
         * Gets formated now
         * @return string
         * @throws Exception
         */
        public static function getDateDefaultTo(): string
        {
            return self::from()->setTime(23, 59, 59)->format(self::DATEFORMAT_PHP);
        }

        public static function getFromRangeTo($range)
        {
            if (empty($range)) {
                try {
                    return self::getDateDefaultTo();
                } catch (Exception $e) {
                }
            }
            $explode = array_map('trim', explode('-', $range));
            if (!empty($explode[1])) {
                return $explode[1];
            }
            /** @noinspection PhpUnhandledExceptionInspection */
            return self::getDateDefaultTo();
        }

        public static function getFromRangeFrom($range)
        {
            if (empty($range)) {
                return self::getDateDefaultFrom();
            }
            $explode = array_map('trim', explode('-', $range));
            if (!empty($explode[0])) {
                return $explode[0];
            }
            return self::getDateDefaultFrom();
        }

        public static function isCurrentMonth(DateTime $date): bool
        {
            $now = new DateTime();
            return $now->format('Y-m') === $date->format('Y-m');
        }

        public static function getTimezoneList(): array
        {
            static $regions = [
                DateTimeZone::AFRICA,
                DateTimeZone::AMERICA,
                //DateTimeZone::ANTARCTICA,
                DateTimeZone::ASIA,
                //DateTimeZone::ATLANTIC,
                DateTimeZone::AUSTRALIA,
                DateTimeZone::EUROPE,
                DateTimeZone::INDIAN,
                //DateTimeZone::PACIFIC,
            ];

            $timezones = [];
            foreach ($regions as $region) {
                /** @noinspection SlowArrayOperationsInLoopInspection */
                $timezones = array_merge($timezones, DateTimeZone::listIdentifiers($region));
            }

            $timezone_offsets = [];
            foreach ($timezones as $timezone) {
                $timezone_offsets[$timezone] = (new DateTimeZone($timezone))->getOffset(new DateTime);
            }
            asort($timezone_offsets);

            $timezone_list = [];
            foreach ($timezone_offsets as $timezone => $offset) {
                $offset_prefix = $offset < 0 ? '-' : '+';
                $offset_formatted = gmdate('H:i', abs($offset));

                $pretty_offset = "GMT${offset_prefix}${offset_formatted}";

                $timezone_list[$timezone] = "(${pretty_offset}) $timezone";
            }
            return $timezone_list;
        }
    }
