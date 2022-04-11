<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 2022-04-11
 */

namespace AllThings\ControlPanel\Category;

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
        $characteristics =
            new Characteristics($this->db, $this->category);
        $characteristics->expand(array_keys($features));

        $positions = new Positions($this->db, $this->category);
        foreach ($features as $feature => $default) {
            $positions->expand($feature, $default);
        }
    }

    public function reduce(array $features)
    {
        $characteristics =
            new Characteristics($this->db, $this->category);
        $characteristics->reduce($features);

        $positions = new Positions($this->db, $this->category);
        foreach ($features as $feature) {
            $positions->reduce($feature);
        }
    }

    public function delete()
    {
        $positions = new Positions($this->db, $this->category);
        $things = $positions->delete();

        $characteristics =
            new Characteristics($this->db, $this->category);
        $characteristics->delete();

        return $things;
    }

    public function remove(string $product)
    {
        $characteristics =
            new Characteristics($this->db, $this->category);
        $features = $characteristics->features();

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