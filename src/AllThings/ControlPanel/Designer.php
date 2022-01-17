<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 17.01.2022, 7:56
 */

namespace AllThings\ControlPanel;

use AllThings\Blueprint\Attribute\AttributeFactory;
use AllThings\Blueprint\Attribute\AttributeManager;
use AllThings\Blueprint\Attribute\IAttribute;
use AllThings\Blueprint\Essence\EssenceFactory;
use AllThings\Blueprint\Essence\EssenceManager;
use AllThings\Blueprint\Essence\IEssence;
use AllThings\StorageEngine\Storable;
use Exception;
use PDO;

class Designer
{
    private PDO $db;

    public function __construct(PDO $connection)
    {
        $this->db = $connection;
    }

    /**
     * @param string $code
     * @param string $title
     * @param string $description
     * @param string $storageKind
     *
     * @return IEssence
     * @throws Exception
     */
    public function essence(
        string $code,
        string $title = '',
        string $description = '',
        string $storageKind = Storable::DIRECT_READING
    ): IEssence {
        $handler = new EssenceManager($this->db);
        $handler->setLocation('essence');
        $handler->setSource('essence');
        $handler->setUniqueIndex('code');
        /** @noinspection PhpUnusedLocalVariableInspection */
        $isSuccess = $handler->create($code);
        /*        if (!$isSuccess) {
                    throw new Exception('Essence must be created with success');
                }*/

        $essence = (new EssenceFactory())
            ->setStorageManner($storageKind)
            ->setCode($code)
            ->setTitle($title)
            ->setRemark($description)
            ->makeEssence();

        if ($storageKind || $title || $description) {
            /** @noinspection PhpUnusedLocalVariableInspection */
            $isSuccess = $handler->correct($essence);
        }
        /*        if (!$isSuccess) {
                    throw new Exception('Essence must be updated with success');
                }*/

        return $essence;
    }

    /**
     * @throws Exception
     */
    public function attribute(
        string $code,
        string $dataType,
        string $rangeType,
        string $title = '',
        string $description = ''
    ): IAttribute {
        $attributeManager = new AttributeManager(
            $this->db,
            'attribute',
        );

        /** @noinspection PhpUnusedLocalVariableInspection */
        $isSuccess = $attributeManager->create($code);
        /*        if (!$isSuccess) {
                    throw new Exception(
                        'Attribute must be created with success'
                    );
                }*/

        $attribute = (new AttributeFactory())
            ->setCode($code)
            ->setDataType($dataType)
            ->setRangeType($rangeType)
            ->setTitle($title)
            ->setRemark($description)
            ->makeAttribute();

        /** @noinspection PhpUnusedLocalVariableInspection */
        $isSuccess = $attributeManager->correct($attribute);
        /*        if (!$isSuccess) {
                    throw new Exception(
                        'Attribute must be updated with success'
                    );
                }*/

        return $attribute;
    }
}