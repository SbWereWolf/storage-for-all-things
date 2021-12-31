<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 31.12.2021, 13:39
 */

declare(strict_types=1);

/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 20.11.2021, 3:41
 */

use AllThings\Blueprint\Attribute\AttributeManager;
use AllThings\Blueprint\Essence\Essence;
use AllThings\Blueprint\Essence\EssenceManager;
use AllThings\Blueprint\Essence\IEssence;
use AllThings\ControlPanel\Browser;
use AllThings\ControlPanel\Operator;
use AllThings\ControlPanel\Schema;
use AllThings\DataAccess\Crossover\Crossover;
use AllThings\DataAccess\Nameable\Nameable;
use AllThings\SearchEngine\ContinuousFilter;
use AllThings\SearchEngine\DiscreteFilter;
use AllThings\SearchEngine\Searchable;
use AllThings\SearchEngine\Seeker;
use AllThings\StorageEngine\Storable;
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

function addNewItem(Operator $operator, $essence): array
{
    $maxVal = PHP_FLOAT_MIN;
    $minVal = PHP_FLOAT_MAX;
    $maxKey = -1;
    $minKey = -1;
    $runs = [];
    for ($i = 0; $i < 5; $i++) {
        $start = microtime(true);

        $suffix = $start;
        $thing = $operator->createItem(
            $essence,
            'new-thing-' . $suffix,
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
 * @param string $thing
 * @param Schema $schema
 * @param IEssence $essence
 * @throws Exception
 */
function defineThing(
    array $kinds,
    Operator $operator,
    Nameable $thing,
    Schema $schema,
    IEssence $essence,
): void {
    $r = $essence->getStorageKind() === Storable::RAPID_RECORDING;
    foreach ($kinds as $kind) {
        if ($kind->getDataType() === Searchable::SYMBOLS
        ) {
            $value = uniqid();
            $operator->changeContent(
                $thing->getCode(),
                $kind->getCode(),
                $value,
            );
        }
        if ($kind->getDataType() === Searchable::DECIMAL
        ) {
            $value = (string)roll(1111, 9999);
            $operator->changeContent(
                $thing->getCode(),
                $kind->getCode(),
                $value,
            );
        }
        if ($r) {
            $schema->refresh(
                (new Crossover())
                    ->setLeftValue($thing->getCode())
                    ->setRightValue($kind->getCode())
                    ->setContent($value)
            );
        }
    }
}

function setupThing(
    array $kinds,
    Operator $operator,
    Nameable $thing,
    Schema $schema,
    IEssence $essence,
): float {
    $maxVal = PHP_FLOAT_MIN;
    $minVal = PHP_FLOAT_MAX;
    $maxKey = -1;
    $minKey = -1;
    $runs = [];
    for ($i = 0; $i < 5; $i++) {
        $start = microtime(true);

        defineThing($kinds, $operator, $thing, $schema, $essence);
        $r = $essence->getStorageKind() ===
            Storable::RAPID_OBTAINMENT;
        if ($r) {
            $schema->refresh();
        }

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

$linkToData = (new DbConnection())->getForWrite();
$browser = new Browser($linkToData);
$operator = new Operator($linkToData);

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
    echo 'MAKE MATH VIEW ' . $average . PHP_EOL;

    try {
        [$dummy, $average] = getFilters($browser, $essence);
    } catch (Exception $e) {
        var_dump($e);
    }
    echo 'GET FILTERS FROM MATH VIEW ' . $average . PHP_EOL;

    try {
        $average = filterData($browser, $essence, []);
    } catch (Exception $e) {
        var_dump($e);
    }
    echo 'TAKE ALL FROM MATH VIEW ' . $average . PHP_EOL;

    try {
        $average = filterData($browser, $essence, $filters);
    } catch (Exception $e) {
        var_dump($e);
    }
    echo 'TAKE SOME FROM MATH VIEW ' . $average . PHP_EOL;

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

    [$average, $thing] = addNewItem($operator, $essence);
    echo 'ADD NEW ITEM ' . $average . PHP_EOL;


    $essenceEntity = (Essence::GetDefaultEssence());
    $essenceEntity->setCode($essence);
    $essenceEntity->setStorageKind(Storable::RAPID_OBTAINMENT);

    $manager = new EssenceManager($essenceEntity, $linkToData);
    $manager->correct($essenceEntity->getCode());

    $start = microtime(true);

    $schema->refresh();

    $finish = microtime(true);
    $duration = $finish - $start;

    echo 'ADD NEW ITEM TO MATH VIEW ' . $duration . PHP_EOL;

    $essenceEntity->setStorageKind(Storable::RAPID_RECORDING);

    $manager = new EssenceManager($essenceEntity, $linkToData);
    $manager->correct($essenceEntity->getCode());

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
        $manager = new AttributeManager($attribute, $linkToData);
        $manager->browse();
        $kinds[$key] = $manager->retrieveData();
    }

    $essenceEntity->setStorageKind(Storable::DIRECT_READING);

    $manager = new EssenceManager($essenceEntity, $linkToData);
    $manager->correct($essenceEntity->getCode());

    $average = setupThing(
        $kinds,
        $operator,
        $thing,
        $schema,
        $essenceEntity
    );

    echo 'SETUP NEW ITEM FOR VIEW ' . $average . PHP_EOL;

    $essenceEntity->setStorageKind(Storable::RAPID_OBTAINMENT);

    $manager = new EssenceManager($essenceEntity, $linkToData);
    $manager->correct($essenceEntity->getCode());

    $average = setupThing(
        $kinds,
        $operator,
        $thing,
        $schema,
        $essenceEntity
    );

    echo 'SETUP NEW ITEM FOR MATH VIEW ' . $average . PHP_EOL;

    $essenceEntity->setStorageKind(Storable::RAPID_RECORDING);

    $manager = new EssenceManager($essenceEntity, $linkToData);
    $manager->correct($essenceEntity->getCode());

    $average = setupThing(
        $kinds,
        $operator,
        $thing,
        $schema,
        $essenceEntity
    );

    echo 'SETUP NEW ITEM FOR TABLE ' . $average . PHP_EOL;

    $adjective = 'test-' . time() . uniqid();
    $attribute = $operator->createKind(
        $adjective,
        Searchable::SYMBOLS,
        Searchable::DISCRETE
    );
    $operator->attachKind(
        $essence,
        $attribute->getCode()
    );

    $essenceEntity->setStorageKind(Storable::DIRECT_READING);

    $manager = new EssenceManager($essenceEntity, $linkToData);
    $manager->correct($essenceEntity->getCode());

    $start = microtime(true);

    $schema->setup();

    $finish = microtime(true);
    $duration = $finish - $start;

    echo 'ADD NEW KIND FOR VIEW ' . $duration . PHP_EOL;

    $essenceEntity->setStorageKind(Storable::RAPID_OBTAINMENT);

    $manager = new EssenceManager($essenceEntity, $linkToData);
    $manager->correct($essenceEntity->getCode());

    $start = microtime(true);

    $schema->setup();

    $finish = microtime(true);
    $duration = $finish - $start;

    echo 'ADD NEW KIND FOR MATH VIEW ' . $duration . PHP_EOL;

    $essenceEntity->setStorageKind(Storable::RAPID_RECORDING);

    $manager = new EssenceManager($essenceEntity, $linkToData);
    $manager->correct($essenceEntity->getCode());

    $start = microtime(true);

    $schema->setup($attribute);

    $finish = microtime(true);
    $duration = $finish - $start;

    echo 'ADD NEW KIND FOR TABLE ' . $duration . PHP_EOL;
}