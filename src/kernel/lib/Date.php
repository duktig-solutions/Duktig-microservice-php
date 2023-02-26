<?php
/**
 * Date Utility class
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

}