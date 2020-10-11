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
	 * A sweet interval formatting, will use the two biggest interval parts.
	 * On small intervals, you get minutes and seconds.
	 * On big intervals, you get months and days.
	 * Only the two biggest parts are used.
	 *
	 * @param DateTime $start
	 * @param DateTime|null $end
	 * @return string
     * @deprecated
	 */
	public static function formatDateDiff($start, $end=null) {

		if(!($start instanceof \DateTime)) {
			$start = new \DateTime($start);
		}

		if($end === null) {
			$end = new \DateTime();
		}

		if(!($end instanceof \DateTime)) {
			$end = new \DateTime($start);
		}

		$interval = $end->diff($start);

		$doPlural = function($nb, $str) {
			return $nb > 1 ? $str . 's' : $str;
		}; // adds plurals

		$format = array();
		if($interval->y !== 0) {
			$format[] = "%y ".$doPlural($interval->y, "year");
		}
		if($interval->m !== 0) {
			$format[] = "%m ".$doPlural($interval->m, "month");
		}
		if($interval->d !== 0) {
			$format[] = "%d ".$doPlural($interval->d, "day");
		}
		if($interval->h !== 0) {
			$format[] = "%h ".$doPlural($interval->h, "hour");
		}
		if($interval->i !== 0) {
			$format[] = "%i ".$doPlural($interval->i, "minute");
		}
		if($interval->s !== 0) {
			if(!count($format)) {
				//return "less than a minute ago";
				//return "less than a minute ago";
				$format[] = "%s ".$doPlural($interval->s, "second");
			} else {
				$format[] = "%s ".$doPlural($interval->s, "second");
			}
		}

		// We use the two biggest parts
		if(count($format) > 1) {
			$format = array_shift($format);//." and ".array_shift($format);
		} else {
			$format = array_pop($format);
		}

		// Prepend 'since ' or whatever you like
		return $interval->format($format);
	}

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