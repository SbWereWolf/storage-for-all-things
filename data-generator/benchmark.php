<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 2022-04-21
 */

declare(strict_types=1);

use AllThings\ControlPanel\Browser;
use AllThings\ControlPanel\Category\Category;
use AllThings\ControlPanel\Designer;
use AllThings\DataAccess\Crossover\Crossover;
use AllThings\DataAccess\Nameable\Nameable;
use AllThings\SearchEngine\ContinuousFilter;
use AllThings\SearchEngine\DiscreteFilter;
use AllThings\SearchEngine\Searchable;
use AllThings\SearchEngine\Seeker;
use AllThings\StorageEngine\RapidRecording;
use AllThings\StorageEngine\Storable;
use AllThings\StorageEngine\StorageManager;
use Environment\Database\PdoConnection;

$pathParts = [__DIR__, '..', 'vendor', 'autoload.php',];
$path = implode(DIRECTORY_SEPARATOR, $pathParts);
require_once($path);

$pathParts = [__DIR__, '..', 'configuration', 'pdo.env',];
$path = implode(DIRECTORY_SEPARATOR, $pathParts);
$linkToData = (new PdoConnection($path))->get();

$browser = new Browser($linkToData);

$essences = [
    'MANY' => 'shelf',
    'AVERAGE' => 'restaurant',
    'FEW' => 'mirror',
];

foreach ($essences as $type => $essence) {
    echo PHP_EOL .
        'Benchmark with ' .
        $essence .
        "($type)" .
        PHP_EOL;
    $schema = new StorageManager($linkToData, $essence);
    $average = setupSource($schema, 'handleWithDirectReading');
    echo 'MAKE VIEW ' . $average . PHP_EOL;

    $filters = [];
    try {
        [$filters, $average] = getFilters($browser, $essence);
    } catch (Exception $e) {
        var_dump($e);
    }
    echo 'GET FILTERS FROM VIEW ' . $average . PHP_EOL;
    $filters = reduceFilters($filters);

    try {
        $average = filterData($browser, $essence, []);
    } catch (Exception $e) {
        var_dump($e);
    }
    echo 'TAKE ALL FROM VIEW ' . $average . PHP_EOL;

    try {
        $average = filterData($browser, $essence, $filters);
    } catch (Exception $e) {
        var_dump($e);
    }
    echo 'TAKE SOME FROM VIEW ' . $average . PHP_EOL;

    $average = setupSource($schema, 'handleWithRapidObtainment');
    echo 'MAKE MAT VIEW ' . $average . PHP_EOL;

    try {
        [$dummy, $average] = getFilters($browser, $essence);
    } catch (Exception $e) {
        var_dump($e);
    }
    echo 'GET FILTERS FROM MAT VIEW ' . $average . PHP_EOL;

    try {
        $average = filterData($browser, $essence, []);
    } catch (Exception $e) {
        var_dump($e);
    }
    echo 'TAKE ALL FROM MAT VIEW ' . $average . PHP_EOL;

    try {
        $average = filterData($browser, $essence, $filters);
    } catch (Exception $e) {
        var_dump($e);
    }
    echo 'TAKE SOME FROM MAT VIEW ' . $average . PHP_EOL;

    $average = setupSource($schema, 'handleWithRapidRecording');
    echo 'MAKE TABLE ' . $average . PHP_EOL;

    try {
        [$dummy, $average] = getFilters($browser, $essence);
    } catch (Exception $e) {
        var_dump($e);
    }
    echo 'GET FILTERS FROM TABLE ' . $average . PHP_EOL;

    try {
        $average = filterData($browser, $essence, []);
    } catch (Exception $e) {
        var_dump($e);
    }
    echo 'TAKE ALL FROM TABLE ' . $average . PHP_EOL;

    try {
        $average = filterData($browser, $essence, $filters);
    } catch (Exception $e) {
        var_dump($e);
    }
    echo 'TAKE SOME FROM TABLE ' . $average . PHP_EOL;

    /* @var Nameable $thing */

    $noun = 'new-thing-' . time() . uniqid();

    $source = $schema->getHandler();
    $seeker = new Seeker($source);
    $kinds = $seeker->getParams(['range_type', 'data_type']);
    $category = new Category($linkToData, $essence);
    [$average, $thing] = addNewItem($linkToData, $noun, $category, $kinds);
    echo 'ADD NEW ITEM ' . $average . PHP_EOL;

    $schema->change(Storable::RAPID_OBTAINMENT);

    $start = microtime(true);

    $schema->refresh();

    $finish = microtime(true);
    $duration = $finish - $start;

    echo 'ADD NEW ITEM TO MAT VIEW ' . $duration . PHP_EOL;

    $schema->change(Storable::RAPID_RECORDING);

    $start = microtime(true);

    $schema->refresh();

    $finish = microtime(true);
    $duration = $finish - $start;

    echo 'ADD NEW ITEM TO TABLE ' . $duration . PHP_EOL;

    $schema->change(Storable::DIRECT_READING);

    $average = setupThing(
        $kinds,
        $category,
        $thing,
        $schema,
    );

    echo 'SETUP NEW ITEM FOR VIEW ' . $average . PHP_EOL;

    $schema->change(Storable::RAPID_OBTAINMENT);

    $average = setupThing(
        $kinds,
        $category,
        $thing,
        $schema,
    );

    echo 'SETUP NEW ITEM FOR MAT VIEW ' . $average . PHP_EOL;

    $schema->change(Storable::RAPID_RECORDING);

    $average = setupThing(
        $kinds,
        $category,
        $thing,
        $schema,
    );

    echo 'SETUP NEW ITEM FOR TABLE ' . $average . PHP_EOL;

    $adjective = 'test-' . time() . uniqid();
    $attribute = (new Designer($linkToData))->attribute(
        $adjective,
        Searchable::SYMBOLS,
        Searchable::DISCRETE,
    );


    $schema->change(Storable::DIRECT_READING);
    $category->expand([$adjective => 'default']);


    $start = microtime(true);

    $schema->setup();

    $finish = microtime(true);
    $duration = $finish - $start;

    echo 'ADD NEW KIND FOR VIEW ' . $duration . PHP_EOL;

    $schema->change(Storable::RAPID_OBTAINMENT);

    $start = microtime(true);

    $schema->setup();

    $finish = microtime(true);
    $duration = $finish - $start;

    echo 'ADD NEW KIND FOR MAT VIEW ' . $duration . PHP_EOL;

    $schema->change(Storable::RAPID_RECORDING);

    $start = microtime(true);

    $schema->setup($adjective, Searchable::SYMBOLS);

    $finish = microtime(true);
    $duration = $finish - $start;

    echo 'ADD NEW KIND FOR TABLE ' . $duration . PHP_EOL;
}

/**
 * @param StorageManager $schema
 * @param string $fn
 *
 * @return float
 */
function setupSource(StorageManager $schema, string $fn): float
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
        $browser->find($essence, $filters);
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
                $filter->getDataType(),
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
                $filter->getDataType(),
                $vals
            );
            $filters[$key] = $filter;
        }
    }
    return $filters;
}

function addNewItem(PDO $db, $noun, Category $category, array $features): array
{
    $maxVal = PHP_FLOAT_MIN;
    $minVal = PHP_FLOAT_MAX;
    $maxKey = -1;
    $minKey = -1;
    $runs = [];
    for ($i = 0; $i < 5; $i++) {
        $start = microtime(true);

        $suffix = $start;
        $designer = new Designer($db);
        $thing = $designer->thing(
            $noun . $i,
            'новая модель' . $suffix,
        );

        $definition = [];
        foreach ($features as $code => $properties) {
            if ($properties['data_type'] === Searchable::DECIMAL) {
                $definition[$code] = 0;
            }
            if ($properties['data_type'] === Searchable::SYMBOLS) {
                $definition[$code] = '';
            }
        }
        $category->add($thing->getCode(), $definition);

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

    return [$average, $thing];
}

/**
 * @param array $kinds
 * @param Category $category
 * @param Nameable $thing
 * @param StorageManager $schema
 *
 * @return array
 * @throws Exception
 */
function defineThing(
    array $kinds,
    Category $category,
    Nameable $thing,
    StorageManager $schema,
): array {
    $isTable = $schema->getHandler() instanceof RapidRecording;
    $definition = [];
    $result = [];
    foreach ($kinds as $code => $kind) {
        $value = '';
        if ($kind['data_type'] === Searchable::SYMBOLS
        ) {
            $value = uniqid();
        }
        if ($kind['data_type'] === Searchable::DECIMAL
        ) {
            $value = (string)roll(1111, 9999);
        }
        if (!$isTable) {
            $definition[$code] = $value;
        }

        $result[] = (new Crossover())
            ->setLeftValue($thing->getCode())
            ->setRightValue($code)
            ->setContent($value);
    }
    $category->update($thing->getCode(), $definition);

    return $result;
}

function setupThing(
    array $kinds,
    Category $category,
    Nameable $thing,
    StorageManager $schema,
): float {
    $maxVal = PHP_FLOAT_MIN;
    $minVal = PHP_FLOAT_MAX;
    $maxKey = -1;
    $minKey = -1;
    $runs = [];
    for ($i = 0; $i < 5; $i++) {
        $start = microtime(true);

        $data = defineThing($kinds, $category, $thing, $schema);
        $schema->refresh($data);

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

    return $average;
}

/**
 * @param int $min
 * @param int $max
 * @return int
 */
function roll(int $min, int $max): int
{
    try {
        $dice = random_int($min, $max);
    } catch (Exception $e) {
        echo $e->getMessage();
        exit;
    }
    return $dice;
}