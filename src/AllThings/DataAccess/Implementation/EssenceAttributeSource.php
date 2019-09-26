<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 27.05.18 11:51
 */

namespace AllThings\DataAccess\Implementation;


use AllThings\DataAccess\Core\ColumnReader;
use AllThings\DataAccess\Handler\Retrievable;
use AllThings\DataObject\IForeignKey;
use PDO;

class EssenceAttributeSource implements ColumnReader, Retrievable
{
    const ESSENCE_IDENTIFIER = 'essence';
    const ATTRIBUTE_IDENTIFIER = 'attribute';
    private $essenceKey = null;
    private $attributeKey = null;
    private $dataSource;
    private $dataSet = [];

    public function __construct(IForeignKey $essenceKey, IForeignKey $attributeKey, PDO $dataSource)
    {
        $this->essenceKey = $essenceKey;
        $this->attributeKey = $attributeKey;
        $this->dataSource = $dataSource;
    }

    function select(array $linkage): bool
    {
        $essenceTable = $this->essenceKey->getTable();
        $essenceColumn = $this->essenceKey->getColumn();
        $essenceIndex = $this->essenceKey->getIndex();

        $attributeTable = $this->attributeKey->getTable();
        $attributeColumn = $this->attributeKey->getColumn();
        $attributeIndex = $this->attributeKey->getIndex();

        $sqlText = "
select A.$attributeIndex as code from essence_attribute EA 
join $essenceTable E on EA.essence_id = E.$essenceColumn 
join $attributeTable A on EA.attribute_id = A.$attributeColumn
where E.$essenceIndex=:essence_code";

        $connection = $this->dataSource;
        $query = $connection->prepare($sqlText);

        $essenceIdentity = $linkage[self::ESSENCE_IDENTIFIER];
        $query->bindParam(':essence_code', $essenceIdentity);

        $result = $query->execute();

        $data = null;
        $isSuccess = $result === true;
        if ($isSuccess) {

            $data = $query->fetchAll();
        }

        $isSuccess = !empty($data);
        if (!$isSuccess) {
            $result = false;
        }
        if ($isSuccess) {

            foreach ($data as $row) {
                $this->dataSet[] = $row['code'];
            }

        }

        return $result;
    }

    function retrieveData(): array
    {
        $result = $this->dataSet;

        return $result;
    }

    function has(): bool
    {
        return !is_null($this->dataSet);
    }
}
