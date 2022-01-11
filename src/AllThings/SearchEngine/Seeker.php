<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 3:54
 */

namespace AllThings\SearchEngine;


use AllThings\Blueprint\Attribute\AttributeHelper;
use AllThings\DataAccess\Linkage\ForeignKey;
use AllThings\DataAccess\Linkage\Linkage;
use AllThings\DataAccess\Linkage\LinkageManager;
use AllThings\DataAccess\Linkage\LinkageTable;
use AllThings\StorageEngine\Installation;
use PDO;

class Seeker implements Searching
{
    /**
     * @var Installation
     */
    private Installation $source;

    public function __construct(Installation $source)
    {
        $this->setSource($source);
    }

    public function limits(): array
    {
        $marker = new Marker($this->getSource());
        $parameters = $this->getParams();
        $filters = $marker->getBoundaries($parameters);
        $result = $marker->getEnumerations($parameters);

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = array_merge($result, $filters);

        return $result;
    }

    public function seek(array $limits = []): array
    {
        $name = $this->getSource()->name();
        $obtain = "SELECT * from $name";

        $isExists = !empty($limits);
        $where = [];
        if ($isExists) {
            foreach ($limits as $filter) {
                /* @var Filter $filter */
                $attribute = $filter->getAttribute();
                $format = AttributeHelper::getFormat(
                    $attribute,
                    $this
                        ->getSource()
                        ->getDb()
                );


                if ($filter instanceof ContinuousFilter) {
                    $min = $filter->getMin();
                    $max = $filter->getMax();
                    $condition =
                        "(\"$attribute\" between " .
                        "'$min'::$format and '$max'::$format)";
                    $where[] = $condition;
                }

                if ($filter instanceof DiscreteFilter) {
                    $condition = implode(
                        "'::$format,'",
                        $filter->getValues()
                    );
                    $condition = "\"$attribute\" " .
                        "IN ('$condition'::$format)";
                    $where[] = $condition;
                }
            }
        }

        $isExists = !empty($where);
        if ($isExists) {
            $wherePhrase = implode(' AND ', $where);
            $obtain = "$obtain where $wherePhrase";
        }

        $data = $this->readData($obtain);

        return $data;
    }

    public function getParams(): array
    {
        $specificationManager = $this->getSpecificationManager();

        $essence = $this->getSource()->getEssence();
        $linkage = (new Linkage())->setLeftValue($essence);

        $isSuccess = $specificationManager->getAssociated($linkage);
        if ($isSuccess) {
            $isSuccess = $specificationManager->has();
        }
        $attributes = [];
        if ($isSuccess) {
            $attributes = $specificationManager->retrieveData();
        }

        return $attributes;
    }

    /**
     * @return Installation
     */
    private function getSource(): Installation
    {
        return $this->source;
    }

    /**
     * @param Installation $source
     *
     * @return static
     */
    private function setSource(Installation $source): static
    {
        $this->source = $source;

        return $this;
    }

    private function readData(string $sql): array
    {
        $cursor = $this
            ->getSource()
            ->getDb()
            ->query($sql, PDO::FETCH_ASSOC);

        $isSuccess = $cursor !== false;
        if ($isSuccess) {
            $data = $cursor->fetchAll();
        }
        if (!$isSuccess || $data === false) {
            $data = [];
        }

        return $data;
    }

    /**
     * @return LinkageManager
     */
    private function getSpecificationManager(): LinkageManager
    {
        $essenceKey = new ForeignKey(
            'essence',
            'id',
            'code'
        );
        $attributeKey = new ForeignKey(
            'attribute',
            'id',
            'code'
        );
        $specification = new LinkageTable(
            'essence_attribute',
            'essence_id',
            'attribute_id',
        );
        $specificationManager = new LinkageManager(
            $this->getSource()->getDb(),
            $specification,
            $essenceKey,
            $attributeKey,
        );

        return $specificationManager;
    }
}
