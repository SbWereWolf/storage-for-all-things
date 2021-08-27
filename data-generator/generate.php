<?php

declare(strict_types=1);

use AllThings\Blueprint\Attribute\IAttribute;
use AllThings\Blueprint\Essence\IEssence;
use AllThings\ControlPanel\Operator;
use AllThings\SearchEngine\Searchable;
use Environment\Database\DbConnection;

$path = [
    __DIR__,
    '..',
    'vendor',
    'autoload.php',
];
$autoloader = implode(DIRECTORY_SEPARATOR, $path);
require_once($autoloader);

$attributes = file(
    'adjective.txt',
    FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
);
$attributeLimit = count($attributes);
$conn = (new DbConnection())->getForWrite();
$conn->beginTransaction();
$operator = new Operator($conn);
foreach ($attributes as $key => $adjective) {
    $isDiscrete = roll(0, 1) === 0;
    if ($isDiscrete) {
        $attribute = $operator->createKind(
            $adjective,
            Searchable::SYMBOLS,
            Searchable::DISCRETE
        );
    }
    if (!$isDiscrete) {
        $attribute = $operator->createKind(
            $adjective,
            Searchable::DECIMAL,
            Searchable::CONTINUOUS
        );
    }

    $attributes[$key] = $attribute;
}
/* @var IAttribute[] $attributes */

$nouns = file(
    'noun.txt',
    FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
);
$entityLimit = count($nouns);

const MIN = 0;
const MAX = 99;

const LOW = 15;
const HIGH = 84;

const POOR = 5;
const RICH = 40;

const FEW = 3;
const MANY = 24;

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

/* @var IEssence[] $allEssences */
$allEssences = [];
$allKinds = [];
for ($i = 0; $i < $entityLimit; $i++) {
    $dice = roll(MIN, MAX);
    $numbers = roll(POOR * 2, RICH / 2);
    if ($dice < LOW) {
        $numbers = roll(POOR, POOR * 2 - 1);
    }
    if ($dice > HIGH) {
        $numbers = roll(RICH / 2 + 1, RICH);
    }

    /* @var IAttribute[] $kinds */
    $kinds = [];
    for ($n = 1; $n <= $numbers; $n++) {
        $exists = true;
        do {
            $index = roll(0, $attributeLimit - 1);
            $exists = key_exists($index, $kinds);
            if (!$exists) {
                $kinds[$index] = $attributes[$index];
            }
        } while ($exists);
    }

    $essence = $operator->createBlueprint($nouns[$i]);
    $allKinds[$essence->getCode()] = $kinds;
    $allEssences[] = $essence;
    foreach ($kinds as $kind) {
        $operator->attachKind(
            $essence->getCode(),
            $kind->getCode()
        );
    }
}

foreach ($allEssences as $essence) {

    $dice = roll(MIN, MAX);
    $numbers = roll(FEW * 2, MANY / 2);
    if ($dice < LOW) {
        $numbers = roll(FEW, FEW * 2 - 1);
    }
    if ($dice > HIGH) {
        $numbers = roll(MANY / 2 + 1, MANY);
    }
    /* @var IAttribute[] $kinds */
    $kinds = $allKinds[$essence->getCode()];
    for ($n = 0; $n < $numbers; $n++) {
        $item = $essence->getCode() . $n;
        $operator->createItem(
            $essence->getCode(),
            $item
        );
        foreach ($kinds as $kind) {
            $isDiscrete =
                $kind->getRangeType() === Searchable::DISCRETE;
            $val = '';
            if ($isDiscrete) {
                $index = roll(0, $attributeLimit - 1);
                $val = $attributes[$index]->getCode();
            }
            if (!$isDiscrete) {
                $index = roll(0, 99);
                $val = str_pad((string)$index, 2, '0', STR_PAD_LEFT);
            }
            $operator->changeContent($item, $kind->getCode(), $val);
        }
    }
}


$conn->commit();

echo 'Data has generated with success' . PHP_EOL;
