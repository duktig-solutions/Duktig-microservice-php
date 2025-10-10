<?php
namespace Tests\Unit\Lib;

use PHPUnit\Framework\TestCase;
use Lib\Benchmarking;

class BenchmarkingTest extends TestCase
{
    public function test_checkpoints_and_results_reset(): void
    {
        Benchmarking::reset();
        Benchmarking::checkPoint('start');
        usleep(1000);
        Benchmarking::checkPoint('middle');
        usleep(1000);
        Benchmarking::checkPoint('end');

        $results = Benchmarking::getResults();
        $this->assertIsArray($results);
        $this->assertNotSame([], $results);

        $readable = Benchmarking::NanoToTime(1234567);
        $this->assertIsString($readable);

        $mem = Benchmarking::ByteToMem(1024*1024);
        $this->assertIsString($mem);

        Benchmarking::reset();
        $this->assertIsArray(Benchmarking::getResults());
    }
}
