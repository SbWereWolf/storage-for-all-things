<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 12:26
 */

namespace AllThings\DataAccess\Implementation;


use AllThings\DataAccess\Core\EssenceWriter;
use AllThings\Essence\IEssence;
use PDO;

class EssenceLocation implements EssenceWriter
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

    public function insert(IEssence $entity): bool
    {
        $suggestion_code = $entity->getCode();

        $sqlText = 'insert into ' . $this->tableName . ' (code)values(:code)';
        $connection = $this->storageLocation;


        $query = $connection->prepare($sqlText);
        $query->bindParam(':code', $suggestion_code);
        $result = $query->execute();

        return $result;
    }

    public function setIsHidden(IEssence $entity): bool
    {
        $target_code = $entity->getCode();

        $sqlText = 'update ' . $this->tableName . ' set is_hidden = 1 where code = :code';
        $connection = $this->storageLocation;

        $query = $connection->prepare($sqlText);
        $query->bindParam(':code', $target_code);
        $result = $query->execute();

        return $result;
    }

    function update(IEssence $target_entity,
                    IEssence $suggestion_entity): bool
    {
        $target_code = $target_entity->getCode();
        $suggestion_code = $suggestion_entity->getCode();
        $suggestion_title = $suggestion_entity->getTitle();
        $suggestion_remark = $suggestion_entity->getRemark();
        $suggestion_storage = $suggestion_entity->getStoreAt();

        $sqlText = 'update '
            . $this->tableName
            . '
set 
    code=:suggestion_code,
    title=:suggestion_title,
    remark=:suggestion_remark,
    store_at=:suggestion_store_at 
where 
    code=:target_code';
        $connection = $this->storageLocation;

        $query = $connection->prepare($sqlText);
        $query->bindParam(':suggestion_code',
            $suggestion_code);
        $query->bindParam(':suggestion_title',
            $suggestion_title);
        $query->bindParam(':suggestion_remark',
            $suggestion_remark);
        $query->bindParam(':suggestion_store_at',
            $suggestion_storage);
        $query->bindParam(':target_code', $target_code);
        $result = $query->execute();

        return $result;
    }
}
