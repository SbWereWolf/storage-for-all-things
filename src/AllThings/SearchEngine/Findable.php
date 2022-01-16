<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\SearchEngine;

interface Findable
{
    public const SYMBOLS = 'word';
    public const DECIMAL = 'number';
    public const TIMESTAMP = 'time';
    public const INTERVAL = 'interval';

    public const DATA_LOCATIONS = [
        self::SYMBOLS => 'word',
        self::DECIMAL => 'number',
        self::TIMESTAMP => 'time_moment',
        self::INTERVAL => 'time_interval',
    ];

    public const DATA_FORMATS = [
        self::SYMBOLS => 'text',
        self::DECIMAL => 'decimal',
        self::TIMESTAMP => 'timestamptz',
        self::INTERVAL => 'interval',
    ];

    public const FIELD_TYPES = [
        self::SYMBOLS => 'VARCHAR(255)',
        self::DECIMAL => 'DECIMAL(14,4)',
        self::TIMESTAMP => 'TIMESTAMP WITH TIME ZONE',
        self::INTERVAL => 'INTERVAL',
    ];
}