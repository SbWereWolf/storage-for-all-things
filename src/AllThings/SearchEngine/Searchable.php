<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */


namespace AllThings\SearchEngine;


interface Searchable extends Findable
{
    public const UNDEFINED = 'undefined';

    public const DATA_TYPES = [
        self::SYMBOLS,
        self::DECIMAL,
        self::TIMESTAMP,
        self::INTERVAL,
    ];

    public const CONTINUOUS = 'continuous';
    public const DISCRETE = 'discrete';

    public const RANGE_TYPES = [
        self::CONTINUOUS,
        self::DISCRETE,
    ];

    public const DATA_TYPE_FIELD = 'data_type';
    public const RANGE_TYPE_FIELD = 'range_type';

    public function getDataType(): string;

    public function getRangeType(): string;

    public function getSearchableCopy(): Searchable;
}
