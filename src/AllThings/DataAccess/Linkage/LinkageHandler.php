<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 10.01.2022, 6:49
 */

namespace AllThings\DataAccess\Linkage;

use PDO;

class LinkageHandler implements ILinkageHandler

{
    protected PDO $db;
    protected ILinkageTable $tableStructure;
    protected IForeignKey $leftKey;
    protected IForeignKey $rightKey;
    private array $dataSet = [];

    public function __construct(
        IForeignKey $leftKey,
        IForeignKey $rightKey,
        ILinkageTable $tableStructure,
        PDO $db
    ) {
        $this->db = $db;
        $this->tableStructure = $tableStructure;
        $this->leftKey = $leftKey;
        $this->rightKey = $rightKey;
    }

    public function combine(ILinkage $linkage): bool
    {
        $writer = $this->getLinkageWriter();
        $container = $linkage->getLinkageCopy();

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $writer->insert($container);

        return $result;
    }

    private function getLinkageWriter(): LinkageWriter
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $location = new LinkageLocation(
            $this->leftKey,
            $this->rightKey,
            $this->tableStructure,
            $this->db
        );

        return $location;
    }

    public function split(ILinkage $linkage): bool
    {
        $writer = $this->getLinkageWriter();
        $container = $linkage->getLinkageCopy();

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $writer->delete($container);

        return $result;
    }

    public function getRelated(ILinkage $linkage): bool
    {
        $dataSource = new LinkageSource(
            $this->leftKey,
            $this->rightKey,
            $this->tableStructure,
            $this->db,
        );

        $result = $dataSource->getForeignColumn($linkage);

        $this->dataSet = [];
        if ($result && $dataSource->has()) {
            $this->dataSet = $dataSource->retrieveData();
        }

        return $result;
    }

    public function retrieveData(): array
    {
        $result = $this->dataSet;

        return $result;
    }

    public function has(): bool
    {
        return !is_null($this->dataSet);
    }
}
