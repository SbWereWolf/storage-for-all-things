<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 03.07.2021, 17:12
 */


namespace AllThings\SearchEngine;


interface Searchable
{
    public const UNDEFINED = 'undefined';

    public const SYMBOLS = 'symbols';
    public const DECIMAL = 'decimal';
    public const TIMESTAMP = 'timestamp';

    public const DATA_TYPE = [
        self::SYMBOLS,
        self::DECIMAL,
        self::TIMESTAMP,
    ];

    public const CONTINUOUS = 'continuous';
    public const DISCRETE = 'discrete';

    public const RANGE_TYPE = [
        self::CONTINUOUS,
        self::DISCRETE,
    ];

    public function getDataType(): string;

    public function setDataType(string $value): Searchable;

    public function getRangeType(): string;

    public function setRangeType(string $value): Searchable;

    public function getSearchableCopy(): Searchable;
}
