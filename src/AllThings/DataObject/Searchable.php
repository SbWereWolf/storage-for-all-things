<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 22.05.18 23:04
 */


namespace AllThings\DataObject;


interface Searchable
{
    const UNDEFINED = 'undefined';

    const SYMBOLS = 'symbols';
    const DECIMAL = 'decimal';
    const TIMESTAMP = 'timestamp';

    const CONTINUOUS = 'continuous';
    const DISCRETE = 'discrete';

    function getDataType(): string;

    function setDataType(string $value): Searchable;

    function getRangeType(): string;

    function setRangeType(string $value): Searchable;

    function getSearchableCopy(): Searchable;
}
