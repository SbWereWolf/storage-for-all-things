<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 22.05.18 23:04
 */


namespace AllThings\DataObject;


interface Searchable
{
    public const UNDEFINED = 'undefined';

    public const SYMBOLS = 'symbols';
    public const DECIMAL = 'decimal';
    public const TIMESTAMP = 'timestamp';

    public const CONTINUOUS = 'continuous';
    public const DISCRETE = 'discrete';

    public function getDataType(): string;

    public function setDataType(string $value): Searchable;

    public function getRangeType(): string;

    public function setRangeType(string $value): Searchable;

    public function getSearchableCopy(): Searchable;
}
