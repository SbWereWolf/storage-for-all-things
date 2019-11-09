<?php
/**
 * storage-for-all-things
 * Copyright © 2019 Volkhin Nikolay
 * 10.11.19 3:46
 */

namespace AllThings\StorageEngine;


interface Installation
{
    /**
     * Настроить и установить источник
     * @return bool
     */
    public function setup(): bool;

    /**
     * Получить имя источника
     * @return string
     */
    public function name(): string;
}
