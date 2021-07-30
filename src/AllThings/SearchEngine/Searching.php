<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:45
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
