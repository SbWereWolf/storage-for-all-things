<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\DataAccess\Linkage;

use JetBrains\PhpStorm\Pure;
use PDO;

class LinkageHandler
    implements ILinkageHandler

{
    protected PDO $db;
    protected ILinkageTable $table;
    protected IForeignKey $leftKey;
    protected IForeignKey $rightKey;

    public function __construct(
        PDO $db,
        ILinkageTable $table
    ) {
        $this->db = $db;
        $this->table = $table;
        $this->leftKey = $table->getLeftKey();
        $this->rightKey = $table->getRightKey();
    }

    public function combine(ILinkage $linkage): bool
    {
        $writer = $this->getLinkageWriter();
        $container = $linkage->getLinkageCopy();

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $writer->insert($container);

        return $result;
    }

    #[Pure]
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

    public function getRelatedFields(
        ILinkage $linkage,
        string $filed,
    ): array {
        $dataSource = $this->getDataSource();
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $dataSource->getRelatedFields($linkage, $filed);

        return $result;
    }

    /**
     * @return LinkageSource
     */
    #[Pure]
    private function getDataSource(): LinkageSource
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $dataSource = new LinkageSource(
            $this->leftKey,
            $this->rightKey,
            $this->table,
            $this->db,
        );
        return $dataSource;
    }

    public function getRelatedRecords(
        ILinkage $linkage,
        array $fields
    ): array {
        $dataSource = $this->getDataSource();
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $dataSource->getRelatedRecords($linkage, $fields);

        return $result;
    }
}
