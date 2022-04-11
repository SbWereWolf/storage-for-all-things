<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 2022-04-11
 */

namespace AllThings\ControlPanel\Product;

use AllThings\Blueprint\Relation\SpecificationFactory;
use AllThings\ControlPanel\AutoUpdate;
use AllThings\ControlPanel\ForceUpdate;
use Exception;
use PDO;

class ProductionLine
{
    private PDO $db;
    private string $product;
    use AutoUpdate;
    use ForceUpdate;

    /**
     * @param PDO $connection
     * @param string $product
     */
    public function __construct(PDO $connection, string $product)
    {
        $this->db = $connection;
        $this->product = $product;
    }

    /** Удалить продукт
     * @return bool
     * @throws Exception
     */
    public function reduce(array $features, string $catalog = ''): bool
    {
        $isSuccess = (new SpecificationFactory($this->db))
            ->make($this->product)
            ->detach($features);

        if ($isSuccess && $this->shouldAutoUpdate()) {
            $this->forceUpdate($catalog, $this->product, []);
        }

        return $isSuccess;
    }

    /** Задать значения атрибутам продукта
     * @param array $values
     * @param string $catalog
     * @return bool
     * @throws Exception
     */
    public function update(array $values, string $catalog): bool
    {
        $isSuccess = (new SpecificationFactory($this->db))
            ->make($this->product)
            ->define($values);

        if ($isSuccess && $this->shouldAutoUpdate()) {
            $this->forceUpdate($catalog, $this->product, $values);
        }

        return $isSuccess;
    }

    /** Добавить продукт в каталог и задать значения атрибутов
     * @param array $values
     * @param string $catalog
     * @throws Exception
     */
    public function expand(array $values, string $catalog)
    {
        $specification = (new SpecificationFactory($this->db))
            ->make($this->product);

        $features = array_keys($values);
        $isSuccess = $specification->attach($features);

        if ($isSuccess) {
            $isSuccess = $specification->define($values);
        }

        if ($isSuccess && $this->shouldAutoUpdate()) {
            $isSuccess =
                $this->forceUpdate($catalog, $this->product, $values);
        }

        return $isSuccess;
    }
}