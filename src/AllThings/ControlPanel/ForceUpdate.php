<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 4/10/22, 2:45 PM
 */

namespace AllThings\ControlPanel;

use AllThings\DataAccess\Crossover\Crossover;
use AllThings\StorageEngine\StorageManager;
use Exception;

trait ForceUpdate
{
    /** Обновление данных в производном источнике
     * @param string $catalog
     * @param string $product
     * @param array $values
     *
     * @throws Exception
     */
    protected function forceUpdate(
        string $catalog,
        string $product,
        array $values
    ): bool {
        $data = [];
        foreach ($values as $attribute => $value) {
            $content = (new Crossover())->setContent($value);
            $content->setLeftValue($product)
                ->setRightValue($attribute);
            $data[] = $content;
        }

        $schema = new StorageManager($this->db, $catalog,);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $schema->refresh($data);

        return $result;
    }
}