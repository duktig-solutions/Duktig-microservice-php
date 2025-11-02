<?php
/**
 * PHP Functionality benchmark for speed and Memory usage.
 *
 * 1 Second = 1000 Millisecond
 * 1 Millisecond = 1000 Microsecond
 * 1 Microsecond = 1000 Nanosecond
 *
 * @version 1.0.1
 */
namespace Lib;

class Benchmarking {

	/**
	 * Collected Data
	 *
	 * @static
	 * @access private
	 * @var array
	 */
	private static array $points = [];
	private static int $initMem = 0;

	/**
	 * Set check point
	 *
	 * @static
	 * @param string $name
	 * @access public
	 * @return void
	 */
	public static function checkPoint(string $name) : void {

		static::$initMem = is_null(static::$initMem) ? memory_get_usage() : static::$initMem;

		# Add checkpoint data
		static::$points[] = [
			# Name
			'nam' => $name,
			# Nanoseconds
			'nas' => hrtime(true),
			# Memory
			'mem' => memory_get_usage()
		];
	}

	/**
	 * Calculate final results and return
	 *
	 * @static
	 * @access public
	 * @return array
	 */
	public static function getResults() : array {

		static::checkPoint('Finish');

		$pointsResult = [];

		$firstPoint = static::$points[0];
		$maxTime = [
			'pointName' => '',
			'pointTime' => 0
		];

		$maxMem = [
			'pointName' => '',
			'pointMem' => 0
		];

		foreach (static::$points as $index => $point) {

			if($index > 0) {

				$prevPoint = static::$points[$index -1];

				# Time
				$point['nasF'] = static::NanoToTime($point['nas'] - $firstPoint['nas']);
				$point['nasP'] = static::NanoToTime($point['nas'] - $prevPoint['nas']);

				if($point['nas'] - $prevPoint['nas'] > $maxTime['pointTime']) {
					$maxTime['pointTime'] = $point['nas'] - $prevPoint['nas'];
					$maxTime['pointName'] = $point['nam'];
				}

				# Memory
				$point['memF'] = static::ByteToMem($point['mem'] - $firstPoint['mem']);
				$point['memP'] = static::ByteToMem($point['mem'] - $prevPoint['mem']);

				if($point['mem'] - $prevPoint['mem'] > $maxMem['pointMem']) {
					$maxMem['pointName'] = $point['nam'];
					$maxMem['pointMem'] = $point['mem'] - $prevPoint['mem'];
				}

			} else {

				$point['nasF'] = static::NanoToTime(0);
				$point['nasP'] = static::NanoToTime(0);
				$point['memF'] = static::ByteToMem($point['mem'] - static::$initMem);
				$point['memP'] = static::ByteToMem($point['mem'] - static::$initMem);
			}

            $pointsResult[] = $point;
		}

		# Final Result
        return [
            'points' => $pointsResult,
            'summary' => [

                # Total duration of whole benchmarking
                'total_duration' => static::NanoToTime(static::$points[count(static::$points) -1]['nas'] - static::$points[0]['nas']),

                # The checkpoint that takes maximum time to finish
                'max_time_name' => $maxTime['pointName'],
                'max_time' => static::NanoToTime($maxTime['pointTime']),

                # The checkpoint that takes maximum memory use
                'max_mem_name' => $maxMem['pointName'],
                'max_mem' => static::ByteToMem($maxMem['pointMem'])
            ]
        ];

	}

	public static function dumpResultsCli(): void {

        $finalResult = static::getResults();

        $strLen = 12;
        $padLen = 13;
        $linePad  = 82;

        echo " " . str_pad('-', $linePad, '-') . "\n";

        echo " 1 Second = 1000 Millisecond\n";
        echo " 1 Millisecond = 1000 Microsecond\n";
        echo " 1 Microsecond = 1000 Nanosecond\n";

        echo " " . str_pad('-', $linePad, '-') . "\n";

        echo " | " .
            str_pad("Checkpoint", $padLen, ' ') . " | " .
            str_pad("From Start", $padLen, ' ') . " | " .
            str_pad("From Prev", $padLen, ' ') . " | " .
            str_pad("Mem Start", $padLen, ' ') . " | " .
            str_pad("Mem Prev", $padLen, ' ') . " | \n";

        echo " " . str_pad('-', $linePad, '-') . "\n";

        foreach ($finalResult['points'] as $point) {
            echo " | " .
                str_pad(substr($point['nam'], 0, $strLen), $padLen, ' ') . " | " .
                str_pad($point['nasF'], $padLen, ' ') . " | " .
                str_pad($point['nasP'], $padLen, ' ') . " | " .
                str_pad($point['memF'], $padLen, ' ') . " | " .
                str_pad($point['memP'], $padLen, ' ') . " | \n";
        }

        echo " " . str_pad('-', $linePad, '-') . "\n";

        $report = [
            'Total Duration:        ' . str_pad($finalResult['summary']['total_duration'], 55, ' ') .'|',
            'Maximum time P2P name: ' . str_pad($finalResult['summary']['max_time_name'], 55, ' ') .'|',
            'Maximum time P2P:      ' . str_pad($finalResult['summary']['max_time'], 55, ' ') .'|',
            'Maximum mem P2P name:  ' . str_pad($finalResult['summary']['max_mem_name'], 55, ' ') .'|',
            'Maximum mem P2P:       ' . str_pad($finalResult['summary']['max_mem'], 55, ' ') .'|'
        ];

        foreach($report as $line) {
            echo " | " . $line . "\n";
        }

        echo " " . str_pad('-', $linePad, '-') . "\n";

	}

	/**
	 * Reset data
	 *
	 * @static
	 * @access public
	 * @return void
	 */
	public static function reset() : void {
		static::$points = [];
	}

	/**
	 * Convert a Nanoseconds to relevant time.
	 *
	 * @static
	 * @access public
	 * @param $Nanosecond
	 * @return string
	 */
	public static function NanoToTime($Nanosecond): string {

		if($Nanosecond <= 0) {
			return '0 Nano';
		} elseif($Nanosecond <= 1000) {
			return round($Nanosecond, 4) . ' Nan';
		} elseif($Nanosecond <= 1000000) {
			return round(($Nanosecond / 1000) , 4) . ' Mic';
		} elseif($Nanosecond <= 1000000000) {
			return round(($Nanosecond / 1000 / 1000), 4) . ' Mil';
		} elseif($Nanosecond <= 1000000000000) {
			return round(($Nanosecond / 1000 / 1000 / 1000), 4) . ' Sec';
		} elseif($Nanosecond <= (1000000000000 * 60)) {
			return round(($Nanosecond / 1000 / 1000 / 1000 / 60), 4) . ' Min';
		} elseif($Nanosecond <= (1000000000000 * 60 * 60)) {
			return round(($Nanosecond / 1000 / 1000 / 1000 / 60 / 60),4) . ' Hrs';
		}

		return 'More than a hour';
	}

    /**
     * Return formatted memory
     *
     * @access public
     * @param $bytes
     * @return string
     */
    public static function ByteToMem($bytes) : string {

		if($bytes <= 0) {
			return '0 bytes';
		}

		$i = floor(log($bytes) / log(1024));
		$sizes = array('bytes', 'Kb', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

		return sprintf('%.02F', $bytes / pow(1024, $i)) * 1 . ' ' . $sizes[$i];
	}

}