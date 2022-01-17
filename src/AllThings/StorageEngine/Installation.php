<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 17.01.2022, 7:56
 */

namespace AllThings\StorageEngine;


use PDO;

interface Installation
{
    /**
     * Создать структуру для записи данных
     *
     * @param string $attribute
     * @param string $dataType
     *
     * @return bool
     */
    public function setup(
        string $attribute = '',
        string $dataType = ''
    ): bool;

    /**
     * Получить имя источника
     *
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
    public function getDb(): PDO;

    /**
     * Освежить данные в источнике
     *
     * @param array $values
     *
     * @return bool
     */
    public function refresh(array $values = []): bool;

    /**
     *
     * @param string $attribute
     *
     * @return bool
     */
    public function prune(string $attribute): bool;

    public function drop(): bool;
}
