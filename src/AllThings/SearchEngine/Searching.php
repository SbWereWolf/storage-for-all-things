<?php
/**
 * storage-for-all-things
 * Copyright © 2019 Volkhin Nikolay
 * 01.10.2019, 21:29
 */

namespace AllThings\SearchEngine;


interface Searching
{

    public function data(array $filters): array;

    public function filters(): array;
}
