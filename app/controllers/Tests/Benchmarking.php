<?php
/**
 * Testing Application Execution time and memory usage benchmarking
 */
namespace App\Controllers\Tests;

use Lib\Benchmarking as Bm;

class Benchmarking {

	public function test1() {

		Bm::reset();

		/*
		for($i = 1; $i <= 100; $i++) {
			Bm::checkPoint('Point ' . $i);
		}
		*/

		Bm::checkPoint('Start');

		//Bm::checkPoint('Before Sleep 1');
		//sleep(1);
		//Bm::checkPoint('After Sleep 1');
		//Bm::checkPoint('Before Sleep 2');
		//sleep(2);
		//Bm::checkPoint('After Sleep 2');

		$testArr = [];

		Bm::checkPoint('Before Loop');

		for($i = 0; $i <= 1000; $i++) {

			$testArr[] = [
				'int' => $i,
				'str' => str_pad('S', $i * 10)
			];

			if($i == 500) {
				Bm::checkPoint('Half in Loop');
			}

		}

		Bm::checkPoint('After Loop');

		Bm::checkPoint('Before unset');
		//unset($testArr);
		$testArr = [];
		Bm::checkPoint('After unset');

		/*
		Bm::checkPoint('Before sleep');
		sleep(2);
		Bm::checkPoint('After sleep');
		*/

		Bm::checkPoint('Before Loop');

		for($i = 0; $i <= 10000; $i++) {
			/*
			$testArr[] = [
				'int' => $i,
				'str' => str_pad('S', $i * 10)
			];

			if($i == 500) {
				Bm::checkPoint('Half in Loop');
			}
			*/

		}
		Bm::checkPoint('After Loop');

		Bm::dumpResultsCli();

	}

}