<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:46
 */

namespace AllThings\SearchEngine;


use AllThings\Blueprint\Specification\SpecificationManager;
use AllThings\DataAccess\Crossover\Crossover;
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
            ->getLinkToData()
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
                if ($filter instanceof ContinuousFilter) {
                    $attribute = $filter->getAttribute();
                    $min = $filter->getMin();
                    $max = $filter->getMax();
                    $condition =
                        "(\"$attribute\" between '$min' and '$max')";
                    $where[] = $condition;
                }

                if ($filter instanceof DiscreteFilter) {
                    $attribute = $filter->getAttribute();
                    $condition = implode("','", $filter->getValues());
                    $condition = "\"$attribute\" IN ('$condition')";
                    $where[] = $condition;
                }
            }
        }

        $isExists = !empty($where);
        if ($isExists) {
            $wherePhrase = implode(' AND ', $where);
            $obtain = "$obtain where $wherePhrase";
        }

        $cursor = $this
            ->getSource()
            ->getLinkToData()
            ->query($obtain, PDO::FETCH_ASSOC);

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

    private function getPossibleParameters(): array
    {
        $essence = $this->getSource()->getEssence();
        $linkToData = $this->getSource()->getLinkToData();

        $manager = new SpecificationManager($linkToData);
        $linkage = (new Crossover())->setLeftValue($essence);
        $isSuccess = $manager->getAssociated($linkage);
        if ($isSuccess) {
            $isSuccess = $manager->has();
        }
        $attributes = [];
        if ($isSuccess) {
            $attributes = $manager->retrieveData();
        }

        return $attributes;
    }
}
