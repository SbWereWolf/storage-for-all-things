<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 03.07.2021, 17:12
 */

/**
 * Created by PhpStorm.
 * User: СЕРГЕЙ
 * Date: 19.05.2018
 * Time: 13:09
 */

namespace AllThings\StorageEngine;


interface Storable
{
    public const UNDEFINED = 'undefined';

    public const DIRECT_READING = 'view';
    public const RAPID_OBTAINMENT = 'materialized view';
    public const RAPID_RECORDING = 'table';
    public const AVAILABLE = [
        self::DIRECT_READING,
        self::RAPID_OBTAINMENT,
        self::RAPID_RECORDING,
    ];

    public function getStorage(): string;

    public function setStorage(string $value): Storable;

    public function getStorableCopy(): Storable;
}
