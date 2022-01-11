<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 2:31
 */

declare(strict_types=1);

use AllThings\Blueprint\Attribute\IAttribute;
use AllThings\Blueprint\Essence\IEssence;
use AllThings\ControlPanel\Operator;
use AllThings\ControlPanel\Redactor;
use AllThings\ControlPanel\Schema;
use AllThings\SearchEngine\Searchable;
use Environment\Database\PdoConnection;

$path = [
    __DIR__,
    '..',
    'vendor',
    'autoload.php',
];
$autoloader = implode(DIRECTORY_SEPARATOR, $path);
require_once($autoloader);

$names = file(
    'adjective.txt',
    FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
);

const MULTIPLIER = 3;

$namesNumber = count($names);
$attributeLimit = $namesNumber * MULTIPLIER;

$pathParts = [__DIR__, '..', 'configuration', 'pdo.env',];
$path = implode(DIRECTORY_SEPARATOR, $pathParts);

/** @var PDO $conn */
$conn = (new PdoConnection($path))->get();
$conn->beginTransaction();

$attributes = [];
echo date('H:i:s') . ': Starting generating of kinds' . PHP_EOL;
for ($cycle = 0; $cycle < MULTIPLIER; $cycle++) {
    foreach ($names as $key => $adjective) {
        $isDiscrete = roll(0, 1) === 0;
        $adjective = "$adjective$cycle";
        $redactor = new Redactor($conn, $adjective);
        if ($isDiscrete) {
            $attribute = $redactor->create(
                Searchable::SYMBOLS,
                Searchable::DISCRETE,
            );
        }
        if (!$isDiscrete) {
            $attribute = $redactor->create(
                Searchable::DECIMAL,
                Searchable::CONTINUOUS,
            );
        }

        $attributes[$key + ($namesNumber * $cycle)] = $attribute;
    }
}
echo date('H:i:s') . ': Finish generate kinds' . PHP_EOL;
/* @var IAttribute[] $attributes */

$nouns = file(
    'noun.txt',
    FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
);
$entityLimit = count($nouns);

/* границы вероятностей */
const MIN = 0;
const MAX = 99;
const LOW = 15;
const HIGH = 84;

/* количество характеристик */
const POOR = 5;
const RICH = 140;

/* количество моделей */
const FEW = 3;
const MANY = 300;

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
echo date('H:i:s') . ': Starting generating of essences' . PHP_EOL;
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

    $schema = new Schema($conn, $nouns[$i]);
    $essence = $schema->create($nouns[$i],);
    $allKinds[$essence->getCode()] = $kinds;
    $allEssences[] = $essence;
    foreach ($kinds as $kind) {
        $redactor->attach(
            $essence->getCode(),
        );
    }
}

echo date('H:i:s') . ': Finish generate essences' . PHP_EOL;
$conn->commit();


echo date('H:i:s') . ': Starting generating of items' . PHP_EOL;
foreach ($allEssences as $essence) {
    echo date('H:i:s') .
        ": Make items for {$essence->getCode()}" .
        PHP_EOL;
    $conn->beginTransaction();

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
        $operator = new Operator($conn, $item);

        $operator->create($essence->getCode());
        $definition = [];
        foreach ($kinds as $kind) {
            $isDiscrete =
                $kind->getRangeType() === Searchable::DISCRETE;
            $value = '' . PHP_EOL;
            if ($isDiscrete) {
                $index = roll(0, $attributeLimit - 1);
                $value = $attributes[$index]->getCode();
            }
            if (!$isDiscrete) {
                $index = roll(1111, 9999);
                $value = (string)$index;
            }
            $definition[$kind->getCode()] = $value;
        }
        $operator->define($definition);
    }
    $conn->commit();
    echo date('H:i:s') .
        ": Finish items for {$essence->getCode()}" .
        PHP_EOL;
}
echo date('H:i:s') . ': Finish generate items' . PHP_EOL;


echo 'Data has generated with success' . PHP_EOL;
