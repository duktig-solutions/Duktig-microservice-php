<?php
/**
 * Example of application memory usage and execution time benchmarking.
 */
namespace App\Controllers\Examples;

use Lib\Benchmarking as Bm;

/**
 * Class Benchmarking
 *
 * @package App\Controllers\Examples
 */
class Benchmarking {

	public function presentInCli() {

		# First, let's reset the benchmarking data
		Bm::reset();

		# Adding checkpoint start
		Bm::checkPoint('Start');

		# Let's test the timing and do a sleep for 1 second.
		Bm::checkPoint('Before Sleep 1 second');
		sleep(1);
		Bm::checkPoint('After Sleep 1 second');

		# Now let's test the Microseconds sleep
		Bm::checkPoint('Before Sleep 0.5 second');
		usleep(50000);
		Bm::checkPoint('After Sleep 0.5 second');

		# What about memory usage?
		# Making an array with data to test
		$testArr = [];

		Bm::checkPoint('Before Loop/Mem usage');

		for($i = 0; $i <= 10000; $i++) {

			$testArr[] = [
				'int' => $i,
				'str' => str_pad('S', $i * 10)
			];

			if($i == 500) {
				Bm::checkPoint('Half in Loop');
			}

		}

		# Now the memory usage has been changed with defined array
		Bm::checkPoint('After Loop/Mem usage');

		# Let's clean and see difference.
		Bm::checkPoint('Before Memory clean');
		unset($testArr);
		Bm::checkPoint('After Memory clean');

		# Let's run a loop with 0.2 second step interval
		Bm::checkPoint('Before Loop with 0.2 second sleep step');

		for($i = 0; $i <= 10; $i++) {
			usleep(20000);
		}

		Bm::checkPoint('After Loop with 0.2 second sleep step');

		Bm::dumpResultsCli();

	}

}