<?php
/**
 * Bengali Date and Number Converter Helper
 */

if (!defined('ABSPATH')) {
    exit;
}

class Azad_Bengali_Date {

    /**
     * English to Bengali Numbers map
     */
    private static $eng_to_bn_digits = array(
        '0' => '০',
        '1' => '১',
        '2' => '২',
        '3' => '৩',
        '4' => '৪',
        '5' => '৫',
        '6' => '৬',
        '7' => '৭',
        '8' => '৮',
        '9' => '৯'
    );

    /**
     * English to Bengali Months map
     */
    private static $eng_to_bn_months = array(
        'January'   => 'জানুয়ারি',
        'February'  => 'ফেব্রুয়ারি',
        'March'     => 'মার্চ',
        'April'     => 'এপ্রিল',
        'May'       => 'মে',
        'June'      => 'জুন',
        'July'      => 'জুলাই',
        'August'    => 'আগস্ট',
        'September' => 'সেপ্টেম্বর',
        'October'   => 'অক্টোবর',
        'November'  => 'নভেম্বর',
        'December'  => 'ডিসেম্বর',
        'Jan'       => 'জানু',
        'Feb'       => 'ফেব্রু',
        'Mar'       => 'মার্চ',
        'Apr'       => 'এপ্রিল',
        'Jun'       => 'জুন',
        'Jul'       => 'জুলাই',
        'Aug'       => 'আগস্ট',
        'Sep'       => 'সেপ্টে',
        'Oct'       => 'অক্টো',
        'Nov'       => 'নভে',
        'Dec'       => 'ডিসে'
    );

    /**
     * English to Bengali Days map
     */
    private static $eng_to_bn_days = array(
        'Saturday'  => 'শনিবার',
        'Sunday'    => 'রবিবার',
        'Monday'    => 'সোমবার',
        'Tuesday'   => 'মঙ্গলবার',
        'Wednesday' => 'বুধবার',
        'Thursday'  => 'বৃহস্পতিবার',
        'Friday'    => 'শুক্রবার',
        'Sat'       => 'শনি',
        'Sun'       => 'রবি',
        'Mon'       => 'সোম',
        'Tue'       => 'মঙ্গল',
        'Wed'       => 'বুধ',
        'Thu'       => 'বৃহঃ',
        'Fri'       => 'শুক্র'
    );

    /**
     * Convert English digits to Bengali digits.
     *
     * @param string|int $number
     * @return string
     */
    public static function convert_number($number) {
        return strtr((string) $number, self::$eng_to_bn_digits);
    }

    /**
     * Convert a date string or timestamp to Bengali format.
     * E.g.: "11 July 2026" or timestamp -> "১১ জুলাই ২০২৬"
     *
     * @param string|int|null $date_or_timestamp
     * @param string $format 'd F Y' or 'd M Y, l'
     * @return string
     */
    public static function get_bengali_date($date_or_timestamp = null, $format = 'j F Y') {
        if (empty($date_or_timestamp)) {
            $timestamp = current_time('timestamp');
        } elseif (is_numeric($date_or_timestamp)) {
            $timestamp = (int) $date_or_timestamp;
        } else {
            $timestamp = strtotime($date_or_timestamp);
            if (!$timestamp) {
                $timestamp = current_time('timestamp');
            }
        }

        $formatted = date_i18n($format, $timestamp);

        // Replace months
        $formatted = strtr($formatted, self::$eng_to_bn_months);

        // Replace days
        $formatted = strtr($formatted, self::$eng_to_bn_days);

        // Replace numbers
        $formatted = self::convert_number($formatted);

        return $formatted;
    }
}
