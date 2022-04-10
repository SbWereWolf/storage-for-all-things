<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 4/10/22, 2:45 PM
 */

namespace AllThings\ControlPanel;

use AllThings\Blueprint\Relation\BlueprintFactory;
use AllThings\Blueprint\Relation\CatalogFactory;
use AllThings\Blueprint\Relation\SpecificationFactory;
use AllThings\DataAccess\Linkage\ForeignKey;
use AllThings\DataAccess\Linkage\Linkage;
use AllThings\DataAccess\Linkage\LinkageManager;
use AllThings\DataAccess\Linkage\LinkageTable;
use AllThings\DataAccess\Nameable\NamedManager;
use AllThings\StorageEngine\StorageManager;
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
    public function delete(): bool
    {
        $category = $this->catalog();

        $isSuccess = (new CatalogFactory($this->db))
            ->make($category)
            ->detach($this->product);

        if ($isSuccess) {
            $features = (new BlueprintFactory($this->db))
                ->make($category)
                ->list();

            $isSuccess = (new SpecificationFactory($this->db))
                ->make($this->product)
                ->purge($features);
        }

        if ($isSuccess) {
            $manager = new NamedManager($this->db, 'thing');
            $isSuccess = $manager->remove($this->product);
        }

        if ($isSuccess && $this->shouldAutoUpdate()) {
            $schema = new StorageManager($this->db, $category,);
            $isSuccess = $schema->refresh();
        }

        return $isSuccess;
    }

    /** Получить категорию продукта
     * @return string
     */
    private function catalog(): string
    {
        $leftKey = new ForeignKey('thing', 'id', 'code',);
        $rightKey = new ForeignKey('essence', 'id', 'code',);
        $table = new LinkageTable(
            'essence_thing',
            $leftKey,
            $rightKey,
        );
        $manager = new LinkageManager($this->db, $table);

        $linkage = (new Linkage())->setLeftValue($this->product);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $essence = current($manager->getAssociated($linkage));

        return $essence;
    }

    /** Задать значения атрибутам продукта
     * @param array $values
     * @return bool
     * @throws Exception
     */
    public function update(array $values): bool
    {
        $isSuccess = (new SpecificationFactory($this->db))
            ->make($this->product)
            ->define($values);

        if ($isSuccess && $this->shouldAutoUpdate()) {
            $category = $this->catalog();
            $this->forceUpdate($category, $this->product, $values);
        }

        return $isSuccess;
    }

    /** Добавить продукт в каталог и задать значения атрибутов
     * @param string $catalog
     * @param array $values
     * @throws Exception
     */
    public function setup(
        string $catalog,
        array $values = []
    ) {
        $isSuccess = (new CatalogFactory($this->db))
            ->make($catalog)
            ->attach($this->product);

        if ($isSuccess) {
            $specification = (new SpecificationFactory($this->db))
                ->make($this->product);

            $features = array_keys($values);
            $specification->attach($features);
        }

        if ($isSuccess && $values) {
            $isSuccess = $specification->define($values);
        }

        if ($isSuccess && $this->shouldAutoUpdate()) {
            $isSuccess =
                $this->forceUpdate($catalog, $this->product, $values);
        }

        return $isSuccess;
    }
}