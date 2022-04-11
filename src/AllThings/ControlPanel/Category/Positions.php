<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 2022-04-11
 */

namespace AllThings\ControlPanel\Category;

use AllThings\Blueprint\Relation\CatalogFactory;
use AllThings\ControlPanel\Product\ProductionLine;
use Exception;
use PDO;

class Positions
{
    private PDO $db;
    private string $catalog;

    /**
     * @param PDO $connection
     * @param string $catalog
     */
    public function __construct(PDO $connection, string $catalog)
    {
        $this->db = $connection;
        $this->catalog = $catalog;
    }

    /** Удалить каталог (все товары каталога)
     * @return array
     * @throws Exception
     */
    public function delete(array $features)
    {
        $products = (new CatalogFactory($this->db))
            ->make($this->catalog)
            ->list();

        foreach ($products as $product) {
            $productionLine = new ProductionLine($this->db, $product);
            $productionLine->disableAutoUpdate();

            $this->remove($product, $features);
        }

        return $products;
    }

    /** Добавить всем продуктам каталога заданный атрибут
     * @param string $feature
     * @param $default
     * @return array
     * @throws Exception
     */
    public function expand(
        string $feature,
        $default = '',
    ) {
        $isSuccess = true;
        $products = (new CatalogFactory($this->db))
            ->make($this->catalog)
            ->list();

        foreach ($products as $product) {
            if (!$isSuccess) {
                break;
            }
            $productionLine = new ProductionLine($this->db, $product);
            $isSuccess = $productionLine->expand(
                [$feature => $default],
                $this->catalog
            );
        }

        return $products;
    }

    /** Удалить у всех продуктов каталога заданный атрибут
     * @param string $feature
     * @throws Exception
     */
    public function reduce(
        string $feature,
    ) {
        $products = (new CatalogFactory($this->db))
            ->make($this->catalog)
            ->list();

        foreach ($products as $product) {
            $productionLine = new ProductionLine($this->db, $product);
            $productionLine->disableAutoUpdate();
            $productionLine->reduce([$feature]);
        }
    }

    public function add(string $product, array $values)
    {
        $isSuccess = (new CatalogFactory($this->db))
            ->make($this->catalog)
            ->attach($product);

        if ($isSuccess) {
            $isSuccess = (new ProductionLine($this->db, $product))
                ->expand($values, $this->catalog);
        }

        return $isSuccess;
    }

    public function remove(string $product, array $features)
    {
        $isSuccess = (new CatalogFactory($this->db))
            ->make($this->catalog)
            ->detach($product);

        $productionLine = new ProductionLine($this->db, $product);
        $productionLine->enableAutoUpdate();
        $productionLine->reduce($features, $this->catalog);

        return $isSuccess;
    }

    public function update(string $product, array $values)
    {
        (new ProductionLine($this->db, $product))
            ->update($values, $this->catalog);
    }
}