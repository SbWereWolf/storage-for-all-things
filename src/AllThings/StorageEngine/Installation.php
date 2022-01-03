<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 03.01.2022, 6:20
 */

namespace AllThings\StorageEngine;


use AllThings\Blueprint\Attribute\IAttribute;
use PDO;

interface Installation
{
    /**
     * Настроить и установить источник
     * @param IAttribute|null $attribute
     * @return bool
     */
    public function setup(?IAttribute $attribute = null): bool;

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
    public function refresh(array $values = []): bool;
}
