<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 4/10/22, 10:38 PM
 */

namespace AllThings\ControlPanel;

use Exception;
use PDO;

class Category
{

    private PDO $db;
    private string $category;

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
        $redactor = (new Redactor($this->db, $this->category));
        $redactor->expand(array_keys($features));

        $operator = (new Operator($this->db, $this->category));
        foreach ($features as $feature => $default) {
            $operator->expand($feature, $default);
        }
    }

    public function reduce(array $features)
    {
        $redactor = (new Redactor($this->db, $this->category));
        $redactor->reduce($features);

        $operator = (new Operator($this->db, $this->category));
        foreach ($features as $feature) {
            $operator->reduce($feature);
        }
    }

    public function delete()
    {
        $operator = (new Operator($this->db, $this->category));
        $operator->delete();

        $redactor = (new Redactor($this->db, $this->category));
        $redactor->delete();
    }
}