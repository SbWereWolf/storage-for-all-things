<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 15:51
 */

namespace AllThings\DataAccess\Core;


use AllThings\DataObject\Named;

class DataSource implements ValuableReader
{

    private $tableName = '';

    function __construct(string $table)
    {

        $this->tableName = $table;
    }

    function readNamed(Named $entity): bool
    {
        $target_code = $entity->getCode();

        $sqlText = 'select code,title,remark from '
            . $this->tableName
            . ' where code=:target_code';
        $connection = (new DbConnection ())->getForRead();

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
