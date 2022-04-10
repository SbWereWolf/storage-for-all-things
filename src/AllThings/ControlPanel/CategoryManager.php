<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 4/10/22, 2:45 PM
 */

namespace AllThings\ControlPanel;

use AllThings\Blueprint\Relation\BlueprintFactory;
use AllThings\DataAccess\Nameable\NamedManager;
use Exception;
use PDO;

class CategoryManager
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