<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 10.01.2022, 6:49
 */

namespace AllThings\SearchEngine;


use AllThings\Blueprint\Specification\SpecificationManager;
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

    /**
     * @param Installation $source
     *
     * @return self
     */
    private function setSource(Installation $source): self
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @param mixed $sql
     * @return array
     */
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

    public function data(array $filters = []): array
    {
        $name = $this->getSource()->name();
        $obtain = "SELECT * from $name";

        $isExists = !empty($filters);
        $where = [];
        if ($isExists) {
            foreach ($filters as $filter) {
                /* @var Filter $filter */
                $attribute = $filter->getAttribute();
                $format = SpecificationManager::getFormat(
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

    /**
     * @return Installation
     */
    private function getSource(): Installation
    {
        return $this->source;
    }

    public function filters(): array
    {
        $marker = new Marker($this->getSource());
        $parameters = $this->getPossibleParameters();
        $filters = $marker->getBoundaries($parameters);
        $result = $marker->getEnumerations($parameters);

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = array_merge($result, $filters);

        return $result;
    }

    public function getPossibleParameters(): array
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
}
