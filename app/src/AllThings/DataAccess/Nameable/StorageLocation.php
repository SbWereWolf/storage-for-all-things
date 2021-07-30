<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace AllThings\DataAccess\Nameable;


use PDO;

class StorageLocation implements ValuableWriter
{

    private $tableName = '';
    /**
     * @var PDO
     */
    private $storageLocation;

    public function __construct(string $table, PDO $storageLocation)
    {
        $this->tableName = $table;
        $this->storageLocation = $storageLocation;
    }

    public function insert(Nameable $entity): bool
    {
        $suggestion_code = $entity->getCode();

        $sqlText = 'insert into ' . $this->tableName . ' (code)values(:code)';
        $connection = $this->storageLocation;

        $query = $connection->prepare($sqlText);
        $query->bindParam(':code', $suggestion_code);
        $result = $query->execute();

        return $result;
    }

    public function setIsHidden(Nameable $entity): bool
    {
        $target_code = $entity->getCode();

        $sqlText = "
update {$this->tableName} set is_hidden = 1 where code = :code";
        $connection = $this->storageLocation;

        $connection->beginTransaction();
        $query = $connection->prepare($sqlText);
        $query->bindParam(':code', $target_code);
        $result = $query->execute();

        $isSuccess = $result === true;
        if ($isSuccess) {
            $result = $connection->commit();
        }
        if (!$isSuccess) {
            $connection->rollBack();
        }

        return $result;
    }

    public function update(Nameable $target_entity, Nameable $suggestion_entity): bool
    {
        $targetCode = $target_entity->getCode();
        $proposalCode = $suggestion_entity->getCode();
        $proposalTitle = $suggestion_entity->getTitle();
        $proposalRemark = $suggestion_entity->getRemark();

        $letUpdateCode = $targetCode !== $proposalCode;
        $updateCode = '';
        if ($letUpdateCode) {
            $updateCode = 'code = :proposalCode,';
        }

        $sqlText = "
update {$this->tableName} 
set 
    $updateCode
    title = :proposalTitle,
    remark=:proposalRemark
where code=:targetCode
";
        $connection = $this->storageLocation;
        $query = $connection->prepare($sqlText);

        if ($letUpdateCode) {
            $query->bindParam(':proposalCode', $proposalCode);
        }
        $query->bindParam(':proposalTitle', $proposalTitle);
        $query->bindParam(':proposalRemark', $proposalRemark);
        $query->bindParam(':targetCode', $targetCode);

        $result = $query->execute();

        return $result;
    }
}
