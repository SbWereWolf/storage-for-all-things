<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 2022-04-11
 */

namespace AllThings\ControlPanel\Category;

use AllThings\Blueprint\Relation\CatalogFactory;
use AllThings\ControlPanel\AutoUpdate;
use AllThings\ControlPanel\ForceUpdate;
use AllThings\ControlPanel\Product\ProductionLine;
use AllThings\StorageEngine\StorageManager;
use Exception;
use PDO;

class Positions
{
    use AutoUpdate;
    use ForceUpdate;

    private PDO $db;
    private string $catalog;

    /**
     * @param PDO $connection
     * @param string $catalog
     */
    public function __construct(PDO $connection, string $catalog)
    {
        $this->db = $connection;
        $this->enableAutoUpdate();
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
            $line = new ProductionLine($this->db, $product);
            $line->disableAutoUpdate();

            $this->remove($product, $features);
        }

        (new StorageManager($this->db, $this->catalog,))
            ->drop();

        return $products;
    }

    /** Добавить всем продуктам каталога заданный атрибут
     * @param string $feature
     * @param $default
     * @return bool
     * @throws Exception
     */
    public function expand(
        string $feature,
        $default = '',
    ): bool
    {
        $isSuccess = true;

        $schema = new StorageManager($this->db, $this->catalog,);
        if ($this->shouldAutoUpdate()) {
            $isSuccess = $schema->setup($feature);
        }

        $products = (new CatalogFactory($this->db))
            ->make($this->catalog)
            ->list();

        foreach ($products as $product) {
            if (!$isSuccess) {
                break;
            }
            $line = new ProductionLine($this->db, $product);
            $isSuccess = $line->expand(
                [$feature => $default],
                $this->catalog
            );

            if ($isSuccess && $this->shouldAutoUpdate()) {
                $isSuccess = $this->forceUpdate(
                    $this->catalog,
                    $product,
                    [$feature => $default]
                );
            }
        }

        return $isSuccess;
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
            $line = new ProductionLine($this->db, $product);
            $line->disableAutoUpdate();
            $line->reduce([$feature]);
        }
        if ($this->shouldAutoUpdate()) {
            (new StorageManager($this->db, $this->catalog,))
                ->prune($feature);
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