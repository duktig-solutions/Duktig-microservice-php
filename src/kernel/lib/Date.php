<?php
/**
 * Date Utility class
 *
 * @version 1.1.0
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
     * @return mixed
     */
    public static function datesIntersects(string $firstDateStart, string $firstDateEnd, string $secondDateStart, string $secondDateEnd, ?bool $returnTotalPeriod = false) {

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

}