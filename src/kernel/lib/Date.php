<?php
/**
 * Date Utility class
 *
 * @author David A. <support@duktig.solutions>
 * @license see License.md
 * @version 1.2.1
 */
namespace Lib;

/**
 * Class Date
 *
 * @package Lib
 */
class Date {

    /**
     * Return Formatted passed date as: 1 month, 2 minute...
     *
     * @static
     * @access public
     * @param string $date
     * @return string
     */
	public static function timeElapsedString(string $date) : string {

	    $ptime = strtotime($date);

	    $etime = time() - $ptime;

        if ($etime < 1)
        {
            return '0 seconds';
        }

        $a = array( 365 * 24 * 60 * 60  =>  'year',
            30 * 24 * 60 * 60  =>  'month',
            24 * 60 * 60  =>  'day',
            60 * 60  =>  'hour',
            60  =>  'minute',
            1  =>  'second'
        );
        $a_plural = array( 'year'   => 'years',
            'month'  => 'months',
            'day'    => 'days',
            'hour'   => 'hours',
            'minute' => 'minutes',
            'second' => 'seconds'
        );

        foreach ($a as $secs => $str)
        {
            $d = $etime / $secs;
            if ($d >= 1)
            {
                $r = round($d);
                return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str);
            }
        }

        return '';
    }

    /**
     * Check intersection of dates
     *
     * In case if $returnTotalPeriod is false, this method will return only bool - true | false if dates intersect
     * In case if the $returnTotalPeriod value is true and dates intersection matches, this method will return total duration of dates
     *    Example: Start date will be taken from min date start and end date will be taken from max date end.
     *
     *      $firstDateStart:  2023-06-22
     *      $firstDateEnd:    2023-06-28
     *      $secondDateStart: 2023-06-25
     *      $secondDateEnd:   2023-06-30
     *
     *      This will return: start - 2023-06-22, end - 2023-06-30
     *
     * @static
     * @access public
     * @param string $firstDateStart
     * @param string $firstDateEnd
     * @param string $secondDateStart
     * @param string $secondDateEnd
     * @param bool $returnTotalPeriod
     * @return array|bool
     */
    public static function datesIntersects(string $firstDateStart, string $firstDateEnd, string $secondDateStart, string $secondDateEnd, ?bool $returnTotalPeriod = false) : array|bool
    {

        $firstDateStart = strtotime($firstDateStart);
        $firstDateEnd = strtotime($firstDateEnd);
        $secondDateStart = strtotime($secondDateStart);
        $secondDateEnd = strtotime($secondDateEnd);

        if($secondDateStart > $firstDateEnd || $firstDateStart > $secondDateEnd || $firstDateEnd < $firstDateStart || $secondDateEnd < $secondDateStart) {
            return false;
        }

        if(!$returnTotalPeriod) {
            return true;
        }

        // Make the total duration of all dates and return start and end dates.
        $start = min($firstDateStart, $secondDateStart);
        $end = max($firstDateEnd, $secondDateEnd);

        return [
            'start' => date('Y-m-d H:i:s', $start),
            'end' => date('Y-m-d H:i:s', $end)
        ];

    }

    /**
     * Return human-readable passed time duration
     * Can be used by for php strtotime() as argument function
     *
     * @static
     * @access public
     * @param int $time
     * @return string
     */
    public static function timeAgo(int $time) : string {

        $diff = max(0, time() - $time);

        if($diff == 0) {
            return "now";
        } elseif ($diff < 60) {
            return "$diff sec ago";
        } elseif ($diff < 3600) {
            $min = floor($diff / 60);
            return "$min min ago";
        } elseif ($diff < 86400) {
            $hr = floor($diff / 3600);
            return "$hr hour" . ($hr > 1 ? "s" : "") . " ago";
        } elseif ($diff < 604800) {
            $day = floor($diff / 86400);
            return "$day day" . ($day > 1 ? "s" : "") . " ago";
        } elseif ($diff < 2592000) {
            $week = floor($diff / 604800);
            return "$week week" . ($week > 1 ? "s" : "") . " ago";
        } elseif ($diff < 31536000) {
            $month = floor($diff / 2592000);
            return "$month month" . ($month > 1 ? "s" : "") . " ago";
        } else {
            $year = floor($diff / 31536000);
            return "$year year" . ($year > 1 ? "s" : "") . " ago";
        }

    }

}