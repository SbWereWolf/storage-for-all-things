<?php
/**
 * storage-for-all-things
 * Copyright © 2019 Volkhin Nikolay
 * 01.12.19 0:42
 */

namespace AllThings\StorageEngine;


use PDO;

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

    /**
     * @return string
     */
    public function getEssence(): string;

    /**
     * @return PDO
     */
    public function getLinkToData(): PDO;
}
