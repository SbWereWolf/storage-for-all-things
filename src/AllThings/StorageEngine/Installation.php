<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:45
 */

namespace AllThings\StorageEngine;


use AllThings\DataAccess\Crossover\ICrossover;
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

    /**
     * Освежить данные в источнике
     * @return bool
     */
    public function refresh(?ICrossover $value = null): bool;
}