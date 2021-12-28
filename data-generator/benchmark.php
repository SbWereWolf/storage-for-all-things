<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 29.12.2021, 1:52
 */

declare(strict_types=1);

/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 20.11.2021, 3:41
 */

use AllThings\ControlPanel\Browser;
use AllThings\ControlPanel\Schema;
use AllThings\SearchEngine\ContinuousFilter;
use AllThings\SearchEngine\DiscreteFilter;
use Environment\Database\DbConnection;

$path = [
    __DIR__,
    '..',
    'vendor',
    'autoload.php',
];
$autoloader = implode(DIRECTORY_SEPARATOR, $path);
require_once($autoloader);


/**
 * @param Schema $schema
 * @param string $fn
 * @return float
 */
function setupSource(Schema $schema, string $fn): float
{
    $maxVal = PHP_FLOAT_MIN;
    $minVal = PHP_FLOAT_MAX;
    $maxKey = -1;
    $minKey = -1;
    $runs = [];
    for ($i = 0; $i < 5; $i++) {
        $start = microtime(true);
        $schema->$fn();
        $finish = microtime(true);
        $duration = $finish - $start;
        $runs[$i] = $duration;
        if ($duration > $maxVal) {
            $maxVal = $duration;
            $maxKey = $i;
        }
        if ($duration < $minVal) {
            $minVal = $duration;
            $minKey = $i;
        }
    }
    unset($runs[$maxKey]);
    unset($runs[$minKey]);
    $allRuns = 0;
    foreach ($runs as $run) {
        $allRuns += $run;
    }
    /** @noinspection PhpUnnecessaryLocalVariableInspection */
    $average = $allRuns / 3;

    return $average;
}

/**
 * @param Browser $browser
 * @param $essence
 * @return array
 * @throws Exception
 */
function getFilters(Browser $browser, $essence): array
{
    $maxVal = PHP_FLOAT_MIN;
    $minVal = PHP_FLOAT_MAX;
    $maxKey = -1;
    $minKey = -1;
    $runs = [];
    for ($i = 0; $i < 5; $i++) {
        $start = microtime(true);
        $filters = $browser->filters($essence);
        $finish = microtime(true);
        $duration = $finish - $start;
        $runs[$i] = $duration;
        if ($duration > $maxVal) {
            $maxVal = $duration;
            $maxKey = $i;
        }
        if ($duration < $minVal) {
            $minVal = $duration;
            $minKey = $i;
        }
    }
    unset($runs[$maxKey]);
    unset($runs[$minKey]);
    $allRuns = 0;
    foreach ($runs as $run) {
        $allRuns += $run;
    }
    $average = $allRuns / 3;

    return [$filters, $average];
}

/**
 * @param Browser $browser
 * @param string $essence
 * @param array $filters
 * @return float
 * @throws Exception
 */
function filterData(Browser $browser, string $essence, array $filters): float
{
    $maxVal = PHP_FLOAT_MIN;
    $minVal = PHP_FLOAT_MAX;
    $maxKey = -1;
    $minKey = -1;
    $runs = [];
    for ($i = 0; $i < 5; $i++) {
        $start = microtime(true);
        $browser->filterData($essence, $filters);
        $finish = microtime(true);
        $duration = $finish - $start;
        $runs[$i] = $duration;
        if ($duration > $maxVal) {
            $maxVal = $duration;
            $maxKey = $i;
        }
        if ($duration < $minVal) {
            $minVal = $duration;
            $minKey = $i;
        }
    }
    unset($runs[$maxKey]);
    unset($runs[$minKey]);
    $allRuns = 0;
    foreach ($runs as $run) {
        $allRuns += $run;
    }
    /** @noinspection PhpUnnecessaryLocalVariableInspection */
    $average = $allRuns / 3;

    return $average;
}

/**
 * @param mixed $filters
 * @return array
 */
function reduceFilters(mixed $filters): array
{
    $total = count($filters);
    $limit = ceil($total * 0.4 / 2);
    for ($i = 0; $i < $limit; $i++) {
        unset($filters[$i]);
    }
    for ($i = $total - 1; $i > $total - $limit; $i--) {
        unset($filters[$i]);
    }
    foreach ($filters as $key => $filter) {
        if ($filter instanceof ContinuousFilter) {
            $min = (int)$filter->getMin();
            $max = (int)$filter->getMax();
            $min = $min > $max ? $min / 2 : $min * 2;
            $max = $min > $max ? $max * 2 : $max / 2;

            if ($min > $max) {
                $buf = $min;
                $min = $max;
                $max = $buf;
            }

            $filter = new ContinuousFilter(
                $filter->getAttribute(),
                (string)$min,
                (string)$max,
            );
            $filters[$key] = $filter;
        }

        if ($filter instanceof DiscreteFilter) {
            $values = $filter->getValues();
            $limit = ceil(count($values) * 0.4);
            $limit = $limit === 0 ? 1 : $limit;
            $vals = [];
            foreach ($values as $index => $value) {
                if ($index >= $limit) {
                    $vals[] = $value;
                }
            }

            $filter = new DiscreteFilter(
                $filter->getAttribute(),
                $vals
            );
            $filters[$key] = $filter;
        }
    }
    return $filters;
}

$linkToData = (new DbConnection())->getForWrite();
$browser = new Browser($linkToData);

$essences = [
    'MANY' => 'zebra',
    'AVERAGE' => 'sugar',
    'FEW' => 'salad',
];

foreach ($essences as $category => $essence) {
    echo PHP_EOL .
        'Benchmark with ' .
        $essence .
        "($category)" .
        PHP_EOL;
    $schema = new Schema($linkToData, $essence);

    /* MAKE VIEW */
    $average = setupSource($schema, 'handleWithDirectReading');
    echo 'MAKE VIEW ' . $average . PHP_EOL;

    /* GET FILTERS */
    $filters = [];
    try {
        [$filters, $average] = getFilters($browser, $essence);
    } catch (Exception $e) {
        var_dump($e);
    }
    echo 'GET FILTERS FROM VIEW ' . $average . PHP_EOL;
    $filters = reduceFilters($filters);

    /* TAKE ALL */
    try {
        $average = filterData($browser, $essence, []);
    } catch (Exception $e) {
        var_dump($e);
    }
    echo 'TAKE ALL FROM VIEW ' . $average . PHP_EOL;
    /* FILTER DATA */
    try {
        $average = filterData($browser, $essence, $filters);
    } catch (Exception $e) {
        var_dump($e);
    }
    echo 'TAKE SOME FROM VIEW ' . $average . PHP_EOL;

    /* MAKE MATH VIEW */
    $average = setupSource($schema, 'handleWithRapidObtainment');
    echo 'MAKE MATH VIEW ' . $average . PHP_EOL;
    /* GET FILTERS */
    try {
        [$dummy, $average] = getFilters($browser, $essence);
    } catch (Exception $e) {
        var_dump($e);
    }
    echo 'GET FILTERS FROM MATH VIEW ' . $average . PHP_EOL;

    /* TAKE ALL */
    try {
        $average = filterData($browser, $essence, []);
    } catch (Exception $e) {
        var_dump($e);
    }
    echo 'TAKE ALL FROM MATH VIEW ' . $average . PHP_EOL;
    /* FILTER DATA */
    try {
        $average = filterData($browser, $essence, $filters);
    } catch (Exception $e) {
        var_dump($e);
    }
    echo 'TAKE SOME FROM MATH VIEW ' . $average . PHP_EOL;

    /* MAKE TABLE */
    $average = setupSource($schema, 'handleWithRapidRecording');
    echo 'MAKE TABLE ' . $average . PHP_EOL;
    /* GET FILTERS */
    try {
        [$dummy, $average] = getFilters($browser, $essence);
    } catch (Exception $e) {
        var_dump($e);
    }
    echo 'GET FILTERS FROM TABLE ' . $average . PHP_EOL;

    /* TAKE ALL */
    try {
        $average = filterData($browser, $essence, []);
    } catch (Exception $e) {
        var_dump($e);
    }
    echo 'TAKE ALL FROM TABLE ' . $average . PHP_EOL;
    /* FILTER DATA */
    try {
        $average = filterData($browser, $essence, $filters);
    } catch (Exception $e) {
        var_dump($e);
    }
    echo 'TAKE SOME FROM TABLE ' . $average . PHP_EOL;
}