<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 29.05.2021, 4:53
 */

namespace AllThings\RapidRecording;


use AllThings\Essence\Attribute;
use AllThings\Essence\AttributeManager;
use AllThings\Essence\EssenceAttributeManager;
use AllThings\Essence\IAttribute;
use AllThings\StorageEngine\Installation;
use PDO;

class Source implements Installation
{
    const STRUCTURE_PREFIX = 'auto_t_';

    private $essence = '';
    /**
     * @var PDO
     */
    private $linkToData;

    public function __construct(string $essence, PDO $linkToData)
    {
        $this->setEssence($essence)->setLinkToData($linkToData);
    }

    /**
     * @param PDO $linkToData
     *
     * @return self
     */
    private function setLinkToData(PDO $linkToData): self
    {
        $this->linkToData = $linkToData;
        return $this;
    }

    /**
     * @param string $essence
     *
     * @return self
     */
    private function setEssence(string $essence): self
    {
        $this->essence = $essence;
        return $this;
    }

    public function setup(): bool
    {
        $essence = $this->getEssence();
        $linkToData = $this->getLinkToData();

        $essenceManager = new EssenceAttributeManager($essence, '', $linkToData);
        $isSuccess = $essenceManager->getAssociated();
        if ($isSuccess) {
            $isSuccess = $essenceManager->has();
        }
        $attributeCodes = [];
        if ($isSuccess) {
            $attributeCodes = $essenceManager->retrieveData();
        }

        $attributes = [];
        foreach ($attributeCodes as $code) {
            $subject = Attribute::GetDefaultAttribute();
            $subject->setCode($code);
            $attributeManager = new AttributeManager($subject, $linkToData);

            $isSuccess = $attributeManager->browse();
            if ($isSuccess) {
                $isSuccess = $attributeManager->has();
            }
            if ($isSuccess) {
                $attributes[] = $attributeManager->retrieveData();
            }
        }
        $columns = [];
        $columnNames = [];
        foreach ($attributes as $attribute) {
            /* @var IAttribute $attribute */

            $datatype = 'VARCHAR(255)';
            /*
                        switch ($attribute->getDataType()) {
                            case Searchable::DECIMAL:
                                $datatype = 'NUMERIC(8,8)';
                                break;
                            case Searchable::TIMESTAMP:
                                $datatype = 'TIMESTAMP';
                                break;
                        }
            */

            $code = $attribute->getCode();
            $columns[] = "\"{$code}\" $datatype";
            $columnNames[] = "\"{$code}\"";
        }

        $columnsPhase = implode(',', $columns);
        $names = implode(',', $columnNames);
        $tablePrimaryKey = "{$essence}_pk";

        $ddl = "
CREATE TABLE {$this->name()}
(
    thing_id integer REFERENCES thing (id), 
    constraint $tablePrimaryKey primary key (thing_id),
    code VARCHAR(255) NOT NULL,
    $columnsPhase
)
";
        $affected = $linkToData->exec($ddl);
        $result = $affected !== false;

        if ($result) {
            $view = new \AllThings\DirectReading\Source(
                $this->getEssence(),
                $this->getLinkToData()
            );
            /** @noinspection SqlInsertValues */
            $ddl = "
INSERT INTO {$this->name()}(thing_id,code,$names)
SELECT id,code,$names
FROM {$view->name()}
";
            $affected = $linkToData->exec($ddl);
            $result = $affected !== false;
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getEssence(): string
    {
        return $this->essence;
    }

    /**
     * @return PDO
     */
    public function getLinkToData(): PDO
    {
        return $this->linkToData;
    }

    public function name(): string
    {
        $name = self::STRUCTURE_PREFIX . $this->getEssence();

        return $name;
    }

}
