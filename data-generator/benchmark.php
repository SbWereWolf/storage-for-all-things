<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 11.01.2022, 6:09
 */

declare(strict_types=1);

use AllThings\Blueprint\Attribute\AttributeManager;
use AllThings\ControlPanel\Browser;
use AllThings\ControlPanel\Operator;
use AllThings\ControlPanel\Redactor;
use AllThings\ControlPanel\Schema;
use AllThings\DataAccess\Crossover\Crossover;
use AllThings\DataAccess\Nameable\Nameable;
use AllThings\SearchEngine\ContinuousFilter;
use AllThings\SearchEngine\DiscreteFilter;
use AllThings\SearchEngine\Searchable;
use AllThings\SearchEngine\Seeker;
use AllThings\StorageEngine\RapidRecording;
use AllThings\StorageEngine\Storable;
use Environment\Database\PdoConnection;

$pathParts = [__DIR__, '..', 'vendor', 'autoload.php',];
$path = implode(DIRECTORY_SEPARATOR, $pathParts);
require_once($path);

$pathParts = [__DIR__, '..', 'configuration', 'pdo.env',];
$path = implode(DIRECTORY_SEPARATOR, $pathParts);
$linkToData = (new PdoConnection($path))->get();

$browser = new Browser($linkToData);

$essences = [
    'MANY' => 'underclothes',
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

    [$average, $thing] = addNewItem($linkToData, $essence);
    echo 'ADD NEW ITEM ' . $average . PHP_EOL;

    $schema->changeStorage(Storable::RAPID_OBTAINMENT);

    $start = microtime(true);

    $schema->refresh();

    $finish = microtime(true);
    $duration = $finish - $start;

    echo 'ADD NEW ITEM TO MAT VIEW ' . $duration . PHP_EOL;

    $schema->changeStorage(Storable::RAPID_RECORDING);

    $start = microtime(true);

    $schema->refresh();

    $finish = microtime(true);
    $duration = $finish - $start;

    echo 'ADD NEW ITEM TO TABLE ' . $duration . PHP_EOL;

    $source = $schema->getInstallation();
    $seeker = new Seeker($source);
    $kinds = $seeker->getPossibleParameters();

    foreach ($kinds as $key => $code) {
        $attribute = \AllThings\Blueprint\Attribute\Attribute
            ::GetDefaultAttribute();
        $attribute->setCode($code);

        $manager = new AttributeManager(
            $code,
            'attribute',
            $linkToData,
        );
        $manager->setSubject($attribute);

        $manager->browse();
        $kinds[$key] = $manager->retrieveData();
    }

    $schema->changeStorage(Storable::DIRECT_READING);

    $operator = new Operator($linkToData, $thing->getCode());
    $average = setupThing(
        $kinds,
        $operator,
        $thing,
        $schema,
    );

    echo 'SETUP NEW ITEM FOR VIEW ' . $average . PHP_EOL;

    $schema->changeStorage(Storable::RAPID_OBTAINMENT);

    $average = setupThing(
        $kinds,
        $operator,
        $thing,
        $schema,
    );

    echo 'SETUP NEW ITEM FOR MAT VIEW ' . $average . PHP_EOL;

    $schema->changeStorage(Storable::RAPID_RECORDING);

    $average = setupThing(
        $kinds,
        $operator,
        $thing,
        $schema,
    );

    echo 'SETUP NEW ITEM FOR TABLE ' . $average . PHP_EOL;

    $adjective = 'test-' . time() . uniqid();
    $redactor = new Redactor($linkToData, $adjective);
    $attribute = $redactor->create(
        Searchable::SYMBOLS,
        Searchable::DISCRETE,
    );
    $redactor->attach($essence,);

    $schema->changeStorage(Storable::DIRECT_READING);

    $start = microtime(true);

    $schema->setup();

    $finish = microtime(true);
    $duration = $finish - $start;

    echo 'ADD NEW KIND FOR VIEW ' . $duration . PHP_EOL;

    $schema->changeStorage(Storable::RAPID_OBTAINMENT);

    $start = microtime(true);

    $schema->setup();

    $finish = microtime(true);
    $duration = $finish - $start;

    echo 'ADD NEW KIND FOR MAT VIEW ' . $duration . PHP_EOL;

    $schema->changeStorage(Storable::RAPID_RECORDING);

    $start = microtime(true);

    $schema->setup($attribute);

    $finish = microtime(true);
    $duration = $finish - $start;

    echo 'ADD NEW KIND FOR TABLE ' . $duration . PHP_EOL;
}

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

function addNewItem(PDO $db, $essence): array
{
    $maxVal = PHP_FLOAT_MIN;
    $minVal = PHP_FLOAT_MAX;
    $maxKey = -1;
    $minKey = -1;
    $runs = [];
    for ($i = 0; $i < 5; $i++) {
        $start = microtime(true);

        $suffix = $start;
        $operator = new Operator($db, 'new-thing-' . $suffix);
        $thing = $operator->create(
            $essence,
            'новая модель' . $suffix,
        );

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
 * @param Operator $operator
 * @param Nameable $thing
 * @param Schema $schema
 * @return array
 * @throws Exception
 */
function defineThing(
    array $kinds,
    Operator $operator,
    Nameable $thing,
    Schema $schema,
): array {
    $result = [];
    $isTable = $schema->getInstallation() instanceof RapidRecording;
    foreach ($kinds as $kind) {
        $value = '';
        if ($kind->getDataType() === Searchable::SYMBOLS
        ) {
            $value = uniqid();
        }
        if ($kind->getDataType() === Searchable::DECIMAL
        ) {
            $value = (string)roll(1111, 9999);
        }
        if (!$isTable) {
            $operator->define(
                $kind->getCode(),
                $value,
            );
        }

        $result[] = (new Crossover())
            ->setLeftValue($thing->getCode())
            ->setRightValue($kind->getCode())
            ->setContent($value);
    }

    return $result;
}

function setupThing(
    array $kinds,
    Operator $operator,
    Nameable $thing,
    Schema $schema,
): float {
    $maxVal = PHP_FLOAT_MIN;
    $minVal = PHP_FLOAT_MAX;
    $maxKey = -1;
    $minKey = -1;
    $runs = [];
    for ($i = 0; $i < 5; $i++) {
        $start = microtime(true);

        $data = defineThing($kinds, $operator, $thing, $schema);
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