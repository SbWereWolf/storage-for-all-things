<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 3:54
 */

namespace AllThings\SearchEngine;


interface Searching
{

    /**
     * Выполнить поиск
     *
     * @param array $limits
     *
     * @return array
     */
    public function seek(array $limits): array;

    /**
     * Получить границы поиска (возможные значения фильтров)
     *
     * @return array
     */
    public function limits(): array;
}
