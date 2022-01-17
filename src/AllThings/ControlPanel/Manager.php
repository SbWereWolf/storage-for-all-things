<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 17.01.2022, 23:55
 */

namespace AllThings\ControlPanel;

use AllThings\ControlPanel\Relation\BlueprintFactory;
use AllThings\ControlPanel\Relation\CatalogFactory;
use AllThings\ControlPanel\Relation\SpecificationFactory;
use AllThings\DataAccess\Crossover\Crossover;
use AllThings\DataAccess\Linkage\ForeignKey;
use AllThings\DataAccess\Linkage\Linkage;
use AllThings\DataAccess\Linkage\LinkageManager;
use AllThings\DataAccess\Linkage\LinkageTable;
use AllThings\DataAccess\Nameable\NamedManager;
use AllThings\StorageEngine\StorageManager;
use Exception;
use PDO;

class Manager
{
    private PDO $db;
    private bool $letAutoUpdate = true;

    public function __construct(PDO $connection)
    {
        $this->db = $connection;
        $this->enableAutoUpdate();
    }

    /**
     * @throws Exception
     */
    public function setupCategory(string $category, array $features)
    {
        $blueprint = (new BlueprintFactory($this->db))->make($category);
        foreach ($features as $feature) {
            $blueprint->attach($feature);
        }
    }

    /**
     * @param string $essence *
     *
     * @throws Exception
     */
    public function setupProduct(
        string $essence,
        string $product,
        array $values = []
    ) {
        $catalog = (new CatalogFactory($this->db))->make($essence);

        $catalog->attach($product);

        $specification = (new SpecificationFactory($this->db))
            ->make($product);

        $features = array_keys($values);
        $specification->attach($features);

        if ($values) {
            $specification->define($values);
        }

        if ($this->shouldAutoUpdate()) {
            $this->forceUpdate($essence, $product, $values);
        }
    }

    /**
     * @throws Exception
     */
    public function updateProduct(string $product, array $values)
    {
        $specification = (new SpecificationFactory($this->db))
            ->make($product);
        $specification->define($values);

        if ($this->shouldAutoUpdate()) {
            $essence = $this->getCategoryOfProduct($product);
            $this->forceUpdate($essence, $product, $values);
        }
    }

    /**
     * @throws Exception
     */
    public function expandCatalog(
        string $category,
        string $feature,
        string $default = '',
    ) {
        $blueprint = (new BlueprintFactory($this->db))
            ->make($category);
        $blueprint->attach($feature);

        if ($this->shouldAutoUpdate()) {
            $this->setup($category, $feature);
        }

        $catalog = (new CatalogFactory($this->db))->make($category);
        $products = $catalog->list();

        foreach ($products as $product) {
            $specification = (new SpecificationFactory($this->db))
                ->make($product);
            $specification->attach([$feature]);

            if ($default) {
                $values = [$feature => $default];

                $specification->define($values);
                if ($this->shouldAutoUpdate()) {
                    $this->forceUpdate($category, $product, $values);
                }
            }
        }

    }

    /**
     * @throws Exception
     */
    public function pruneCatalog(
        string $category,
        string $feature,
    ) {
        $blueprint = (new BlueprintFactory($this->db))
            ->make($category);
        $blueprint->detach($feature);

        $catalog = (new CatalogFactory($this->db))->make($category);
        $products = $catalog->list();
        foreach ($products as $product) {
            $specification = (new SpecificationFactory($this->db))
                ->make($product);
            $specification->detach([$feature]);
        }
        if ($this->shouldAutoUpdate()) {
            $this->prune($category, $feature);
        }
    }

    /**
     * @throws Exception
     */
    public function deleteProduct(string $product): bool
    {
        $category = $this->getCategoryOfProduct($product);

        $catalog = (new CatalogFactory($this->db))->make($category);

        $catalog->detach($product);

        $features = (new BlueprintFactory($this->db))
            ->make($category)
            ->list();

        $specification = (new SpecificationFactory($this->db))
            ->make($product);
        $specification->purge($features);

        $manager = new NamedManager($this->db, 'thing');
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $manager->remove($product);

        if ($this->shouldAutoUpdate()) {
            $this->refresh($category);
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    public function deleteCategory(string $category): bool
    {
        if ($this->shouldAutoUpdate()) {
            $this->drop($category);
        }

        $blueprint = (new BlueprintFactory($this->db))->make($category);
        $features = $blueprint->list();
        $blueprint->purge();

        $catalog = (new CatalogFactory($this->db))->make($category);
        $products = $catalog->list();
        foreach ($products as $product) {
            $specification = (new SpecificationFactory($this->db))
                ->make($product);
            $specification->purge($features);
        }

        $catalog->purge();
        foreach ($products as $product) {
            $thing = new NamedManager($this->db, 'thing',);
            $thing->remove($product);
        }

        $manager = new NamedManager($this->db, 'essence',);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $manager->remove($category);

        return $result;
    }

    /**
     * @param string $product
     *
     * @return string
     */
    private function getCategoryOfProduct(string $product): string
    {
        $leftKey = new ForeignKey('thing', 'id', 'code',);
        $rightKey = new ForeignKey('essence', 'id', 'code',);
        $table = new LinkageTable(
            'essence_thing',
            $leftKey,
            $rightKey,
        );
        $manager = new LinkageManager($this->db, $table);

        $linkage = (new Linkage())->setLeftValue($product);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $essence = $manager->getAssociated($linkage)[0];

        return $essence;
    }

    /**
     * @param string $essence
     * @param string $product
     * @param array  $values
     *
     * @throws Exception
     */
    public function forceUpdate(
        string $essence,
        string $product,
        array $values
    ): void {
        $data = [];
        foreach ($values as $attribute => $value) {
            $content = (new Crossover())->setContent($value);
            $content->setLeftValue($product)
                ->setRightValue($attribute);
            $data[] = $content;
        }

        $this->refresh($essence, $data);
    }

    /**
     *
     * @return Manager
     */
    public function enableAutoUpdate(): Manager
    {
        $this->letAutoUpdate = true;

        return $this;
    }

    public function disableAutoUpdate(): Manager
    {
        $this->letAutoUpdate = false;

        return $this;
    }

    /**
     * @return bool
     */
    private function shouldAutoUpdate(): bool
    {
        return $this->letAutoUpdate;
    }

    public function setup(string $category, string $feature = ''): bool
    {
        $schema = new StorageManager($this->db, $category,);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $schema->setup($feature);

        return $result;
    }

    public function refresh(string $catalog, array $data = []): bool
    {
        $schema = new StorageManager($this->db, $catalog,);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $schema->refresh($data);

        return $result;
    }

    public function prune(string $catalog, string $feature): bool
    {
        $schema = new StorageManager($this->db, $catalog,);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $schema->prune($feature);

        return $result;
    }

    public function drop(string $category): bool
    {
        $schema = new StorageManager($this->db, $category,);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $schema->drop();

        return $result;
    }
}