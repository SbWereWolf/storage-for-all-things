<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 4/10/22, 3:10 PM
 */

namespace AllThings\ControlPanel;

use AllThings\Blueprint\Relation\BlueprintFactory;
use AllThings\Blueprint\Relation\CatalogFactory;
use AllThings\Blueprint\Relation\SpecificationFactory;
use AllThings\DataAccess\Nameable\NamedManager;
use AllThings\StorageEngine\StorageManager;
use Exception;
use PDO;

class Operator
{
    use AutoUpdate;
    use ForceUpdate;

    private PDO $db;
    private string $catalog;

    public function __construct(PDO $connection, string $catalog)
    {
        $this->db = $connection;
        $this->enableAutoUpdate();
        $this->catalog = $catalog;
    }

    /** Удалить каталог (все товары каталога)
     * @return void
     * @throws Exception
     */
    public function delete(): void
    {
        if ($this->shouldAutoUpdate()) {
            (new StorageManager($this->db, $this->catalog,))
                ->drop();
        }

        $catalog = (new CatalogFactory($this->db))
            ->make($this->catalog);
        $products = $catalog->list();

        $features = (new BlueprintFactory($this->db))
            ->make($this->catalog)
            ->list();

        foreach ($products as $product) {
            (new SpecificationFactory($this->db))
                ->make($product)
                ->purge($features);
        }

        $catalog->purge();
        foreach ($products as $product) {
            (new NamedManager($this->db, 'thing',))
                ->remove($product);
        }
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
    ): bool {
        $isSuccess = true;
        if ($this->shouldAutoUpdate()) {
            $schema = new StorageManager($this->db, $this->catalog,);
            $isSuccess = $schema->setup($feature);
        }

        $products = (new CatalogFactory($this->db))
            ->make($this->catalog)
            ->list();

        foreach ($products as $product) {
            if (!$isSuccess) {
                break;
            }
            $specification = (new SpecificationFactory($this->db))
                ->make($product);
            $isSuccess = $specification->attach([$feature]);

            $values = [];
            if ($isSuccess && $default) {
                $values = [$feature => $default];
                $isSuccess = $specification->define($values);
            }

            if (
                $isSuccess &&
                !empty($values) &&
                $this->shouldAutoUpdate()
            ) {
                $this->forceUpdate($this->catalog, $product, $values);
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
            (new SpecificationFactory($this->db))
                ->make($product)
                ->detach([$feature]);
        }
        if ($this->shouldAutoUpdate()) {
            (new StorageManager($this->db, $this->catalog,))
                ->prune($feature);
        }
    }
}