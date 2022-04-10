<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 2022-04-10
 */

namespace AllThings\ControlPanel\Category;

use AllThings\Blueprint\Relation\BlueprintFactory;
use AllThings\ControlPanel\AutoUpdate;
use AllThings\DataAccess\Nameable\NamedManager;
use Exception;
use PDO;

class Redactor
{
    use AutoUpdate;

    private PDO $db;
    private string $category;

    public function __construct(PDO $connection, string $category)
    {
        $this->db = $connection;
        $this->enableAutoUpdate();
        $this->category = $category;
    }

    /** Удалить категорию и все продукты из этой категории
     * @return bool
     * @throws Exception
     */
    public function delete(): bool
    {
        (new BlueprintFactory($this->db))
            ->make($this->category)
            ->purge();

        $result = (new NamedManager($this->db, 'essence',))
            ->remove($this->category);

        return $result;
    }

    /** Добавить характеристики в категорию
     * @param array $features
     */
    public function expand(array $features)
    {
        $blueprint = (new BlueprintFactory($this->db))
            ->make($this->category);
        foreach ($features as $feature) {
            $blueprint->attach($feature);
        }
    }

    /** Удалить характеристики из категории
     * @param array $features
     */
    public function reduce(array $features)
    {
        $blueprint = (new BlueprintFactory($this->db))
            ->make($this->category);
        foreach ($features as $feature) {
            $blueprint->detach($feature);
        }
    }

}