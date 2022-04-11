<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 2022-04-11
 */

namespace AllThings\ControlPanel\Category;

use AllThings\Blueprint\Relation\BlueprintFactory;
use Exception;
use PDO;

class Category
{
    private PDO $db;
    private string $category;

    /**
     * @param PDO $connection
     * @param string $category
     */
    public function __construct(PDO $connection, string $category)
    {
        $this->db = $connection;
        $this->category = $category;
    }

    /** Расширить категорию новыми фичами со значениями по умолчанию
     * @param array $features [$feature => $default]
     * @return void
     * @throws Exception
     */
    public function expand(array $features)
    {
        $blueprint = (new BlueprintFactory($this->db))
            ->make($this->category);
        foreach (array_keys($features) as $feature) {
            $blueprint->attach($feature);
        }

        $positions = new Positions($this->db, $this->category);
        foreach ($features as $feature => $default) {
            $positions->expand($feature, $default);
        }
    }

    public function reduce(array $features)
    {
        $blueprint = (new BlueprintFactory($this->db))
            ->make($this->category);
        foreach ($features as $feature) {
            $blueprint->detach($feature);
        }

        $positions = new Positions($this->db, $this->category);
        foreach ($features as $feature) {
            $positions->reduce($feature);
        }
    }

    public function delete()
    {
        $blueprint = (new BlueprintFactory($this->db))
            ->make($this->category);
        $features = $blueprint->list();
        $blueprint->purge();

        $positions = new Positions($this->db, $this->category);
        $things = $positions->delete($features);


        return $things;
    }

    public function remove(string $product)
    {
        $features = (new BlueprintFactory($this->db))
            ->make($this->category)
            ->list();

        $positions = new Positions($this->db, $this->category);
        $positions->remove($product, $features);
    }

    public function add(string $product, array $values)
    {
        $positions = new Positions($this->db, $this->category);
        $positions->add($product, $values);
    }

    /**
     * @param string $product
     * @param array $values
     * @return void
     */
    public function update(string $product, array $values)
    {
        $positions = new Positions($this->db, $this->category);
        $positions->update($product, $values);
    }
}