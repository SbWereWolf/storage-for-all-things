<?php
/**
 * storage-for-all-things
 * Copyright © 2019 Volkhin Nikolay
 * 10.11.19 3:46
 */

namespace AllThings\SearchEngine;


interface Searching
{

    /**
     * Получить данные
     * @param array $filters
     *
     * @return array
     */
    public function data(array $filters): array;

    /**
     * Получить возможные условия отбора
     * @return array
     */
    public function filters(): array;
}
