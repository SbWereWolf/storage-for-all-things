<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 26.12.2021, 5:51
 */


namespace AllThings\SearchEngine;


interface Searchable
{
    public const UNDEFINED = 'undefined';

    public const SYMBOLS = 'word';
    public const DECIMAL = 'number';
    public const TIMESTAMP = 'time';
    public const INTERVAL = 'interval';

    public const DATA_TYPE = [
        self::SYMBOLS,
        self::DECIMAL,
        self::TIMESTAMP,
        self::INTERVAL,
    ];

    public const DATA_LOCATION = [
        self::SYMBOLS => 'word',
        self::DECIMAL => 'number',
        self::TIMESTAMP => 'time_moment',
        self::INTERVAL => 'time_interval',
    ];

    public const DATA_FORMAT = [
        self::SYMBOLS => 'text',
        self::DECIMAL => 'decimal',
        self::TIMESTAMP => 'timestamptz',
        self::INTERVAL => 'interval',
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
