<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 17:50
 */

namespace AllThings\DataAccess\Linkage;

use PDO;

class LinkageHandler
    implements ILinkageHandler

{
    protected PDO $db;
    protected ILinkageTable $table;
    protected IForeignKey $leftKey;
    protected IForeignKey $rightKey;

    public function __construct(
        IForeignKey $leftKey,
        IForeignKey $rightKey,
        ILinkageTable $table,
        PDO $db
    ) {
        $this->db = $db;
        $this->table = $table;
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
            $this->table,
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

    public function getRelated(ILinkage $linkage): array
    {
        $dataSource = new LinkageSource(
            $this->leftKey,
            $this->rightKey,
            $this->table,
            $this->db,
        );
        $isSuccess = $dataSource->getForeignColumn($linkage);

        $result = [];
        if ($isSuccess && $dataSource->has()) {
            $result = $dataSource->extract();
        }

        return $result;
    }
}
