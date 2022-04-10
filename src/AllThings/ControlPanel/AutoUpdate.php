<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 4/10/22, 2:45 PM
 */

namespace AllThings\ControlPanel;

trait AutoUpdate
{
    private bool $letAutoUpdate = true;

    /** Поднять флаг автоматического обновления производных данных
     *
     * @return CatalogManager
     */
    public function enableAutoUpdate(): static
    {
        $this->letAutoUpdate = true;

        return $this;
    }

    /** Снять флаг автоматического обновления производных данных
     * @return $this
     */
    public function disableAutoUpdate(): static
    {
        $this->letAutoUpdate = false;

        return $this;
    }

    /** Следует автоматически обновить производные данные ?
     * @return bool
     */
    protected function shouldAutoUpdate(): bool
    {
        return $this->letAutoUpdate;
    }
}