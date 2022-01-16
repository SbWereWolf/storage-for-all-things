<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
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

    public function getStorageManner(): string;

    public function getStorableCopy(): Storable;
}
