# Duktig PHP Microservice - Development documentation

## Libraries - Benchmarking

**Benchmarking** library in Duktig PHP Framework provides functionality to **measure execution time** and **memory usage** between checkpoints in your code.

File: `kernel/lib/Benchmarking.php`  
Class: `Benchmarking`

### Methods

- [checkPoint() - Set checkpoint](#set-checkpoint)
- [getResults() - Get benchmark results](#get-benchmark-results)
- [dumpResultsCli() - Print results in CLI](#print-results-in-cli)
- [reset() - Reset benchmark data](#reset-benchmark-data)
- [NanoToTime() - Convert nanoseconds to readable time](#convert-nanoseconds-to-readable-time)
- [ByteToMem() - Convert bytes to readable memory size](#convert-bytes-to-readable-memory-size)

---

### Set checkpoint

`static void checkPoint(string $name)`

Set a new checkpoint for benchmarking with a name.  
Each checkpoint will record **time** and **memory usage** at the moment of calling.

Arguments:

- `string` Name of the checkpoint

Return value:

- `void`

```php
\Lib\Benchmarking::checkPoint('Start');

// Some code to benchmark
sleep(1);

\Lib\Benchmarking::checkPoint('After 1 second');
```

---

### Get benchmark results

`static array getResults()`

Calculates results between all checkpoints and returns them as an array.  
Includes total duration, memory usage, and detailed stats for each checkpoint.

Return value:

- `array` Benchmarking results

Example result structure:

```php
[
    'points' => [
        [
            'nam' => 'Start',
            'nasF' => '0 Nano',
            'nasP' => '0 Nano',
            'memF' => '0 bytes',
            'memP' => '0 bytes'
        ],
        [
            'nam' => 'After 1 second',
            'nasF' => '1.002 Sec',
            'nasP' => '1.002 Sec',
            'memF' => '12.5 Kb',
            'memP' => '12.5 Kb'
        ]
    ],
    'summary' => [
        'total_duration' => '1.002 Sec',
        'max_time_name' => 'After 1 second',
        'max_time' => '1.002 Sec',
        'max_mem_name' => 'After 1 second',
        'max_mem' => '12.5 Kb'
    ]
]
```

Example usage:

```php
\Lib\Benchmarking::checkPoint('Start');

for($i = 0; $i < 1000000; $i++) {
    $a = $i * 2;
}

\Lib\Benchmarking::checkPoint('Loop End');

$results = \Lib\Benchmarking::getResults();

print_r($results);
```

---

### Print results in CLI

`static void dumpResultsCli()`

Prints formatted benchmark results directly in the command line interface (CLI).  
Shows detailed timing and memory usage between checkpoints.

Return value:

- `void`

Example usage:

```php
\Lib\Benchmarking::checkPoint('Start');

// Some heavy operation
usleep(500000);

\Lib\Benchmarking::checkPoint('Half');
usleep(500000);

\Lib\Benchmarking::checkPoint('End');

\Lib\Benchmarking::dumpResultsCli();
```

Example CLI output:

```
 ----------------------------------------------------------------------------------
 1 Second = 1000 Millisecond
 1 Millisecond = 1000 Microsecond
 1 Microsecond = 1000 Nanosecond
 ----------------------------------------------------------------------------------
 | Checkpoint    | From Start   | From Prev    | Mem Start   | Mem Prev    | 
 ----------------------------------------------------------------------------------
 | Start          | 0 Nano       | 0 Nano       | 0 bytes     | 0 bytes     | 
 | Half           | 0.5 Sec      | 0.5 Sec      | 8.0 Kb      | 8.0 Kb      | 
 | End            | 1.0 Sec      | 0.5 Sec      | 12.0 Kb     | 4.0 Kb      | 
 ----------------------------------------------------------------------------------
 | Total Duration:        1.0 Sec                                          |
 | Maximum time P2P name:  End                                             |
 | Maximum time P2P:      0.5 Sec                                          |
 | Maximum mem P2P name:  End                                              |
 | Maximum mem P2P:       4.0 Kb                                           |
 ----------------------------------------------------------------------------------
```

---

### Reset benchmark data

`static void reset()`

Resets all collected benchmarking data and checkpoints.

Return value:

- `void`

Example usage:

```php
\Lib\Benchmarking::reset();
```

---

### Convert nanoseconds to readable time

`static string NanoToTime($Nanosecond)`

Converts nanoseconds into a readable time format (Nan, Mic, Mil, Sec, Min, Hrs).

Arguments:

- `int|float` Nanoseconds value

Return value:

- `string` Converted time string

Example:

```php
echo \Lib\Benchmarking::NanoToTime(1000000000);
// Output: 1 Sec
```

---

### Convert bytes to readable memory size

`static string ByteToMem($bytes)`

Converts byte values to a readable memory format (Kb, MB, GB, etc.).

Arguments:

- `int` Number of bytes

Return value:

- `string` Formatted memory string

Example:

```php
echo \Lib\Benchmarking::ByteToMem(2048);
// Output: 2.00 Kb
```

---

End of document