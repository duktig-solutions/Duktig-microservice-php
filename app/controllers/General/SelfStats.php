<?php
/**
 * Self Statistics processing controller
 *
 * - Send Self statistics to DataReception
 */
namespace App\Controllers\General;

/**
 * Class SelfStats
 *
 * @package App\Controllers\General
 */
class SelfStats {

	public function sendStatsToDataReception(Input $input, Output $output, array $middlewareResult) : bool {

		// @todo Send Data to DataReception

		return true;

	}

}