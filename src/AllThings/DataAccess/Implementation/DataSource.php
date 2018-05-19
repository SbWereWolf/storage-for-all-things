<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 15:51
 */

namespace AllThings\DataAccess\Core;


use AllThings\DataObject\Nameable;

class DataSource implements ValuableReader
{

    private $tableName = '';
    /**
     * @var \PDO
     */
    private $dataSource;

    function __construct(string $table, \PDO $dataSource)
    {

        $this->tableName = $table;
        $this->dataSource = $dataSource;
    }

    function readNamed(Nameable $entity): bool
    {
        $target_code = $entity->getCode();

        $sqlText = 'select code,title,remark from '
            . $this->tableName
            . ' where code=:target_code';
        $connection = $this->dataSource;

        $connection->beginTransaction();
        $query = $connection->prepare($sqlText);
        $query->bindParam(':target_code', $target_code);
        $result = $query->execute();

        $isSuccess = $result === true;
        if ($isSuccess) {
            $result = $connection->commit();
        }
        if (!$isSuccess) {
            $connection->rollBack();
        }

        $data = null;
        $isSuccess = $result === true;
        if($isSuccess) {

            $data = $query->fetchAll();
        }

        $isSuccess = !empty($data);
        if(!$isSuccess){
            $result = false;
        }
        if($isSuccess){

            $row= $data[0];

            $code = $row['code'];
            $title = $row['title'];
            $remark = $row['remark'];

            $entity->setCode($code);
            $entity->setTitle($title);
            $entity->setRemark($remark);

        }

        return $result;
    }
}
