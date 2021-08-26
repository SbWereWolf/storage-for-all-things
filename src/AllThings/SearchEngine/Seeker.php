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
    private $source;

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

    /**
     * @param array $parameters
     * @return string
     */
    private static function glueUpWithComma(array $parameters): string
    {
        $match = '';
        $isSuccess = count($parameters) !== 0;
        if ($isSuccess) {
            $match .= '\'';
            $match .= implode("','", $parameters);
            $match .= '\'';
        }
        return $match;
    }

    /**
     * @param string $match
     * @param string $rangeType
     * @return array
     */
    private function getSpecificParams(
        string $match,
        string $rangeType
    ): array
    {
        $readParams = "
SELECT
    code
FROM
    attribute
WHERE 
    code IN ($match)
    and range_type = '$rangeType'
ORDER BY code
";

        $specific = $this->readData($readParams);
        $params = [];
        if ($specific) {
            $params = array_column($specific, 'code');
        }

        return $params;
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
                    /* @var $filter ContinuousFilter */
                    $attribute = $filter->getAttribute();
                    $min = $filter->getMin();
                    $max = $filter->getMax();
                    $condition = "(\"$attribute\" between '$min' and '$max')";
                    $where[] = $condition;
                }

                if ($filter instanceof DiscreteFilter) {
                    /* @var $filter DiscreteFilter */
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

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
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
        $parameters = $this->getPossibleParameters();

        $result = [];
        $result['continuous'] = $this->getBoundaries($parameters);
        $result['discrete'] = $this->getValues($parameters);

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

    /**
     * @param array $parameters
     * @return array
     */
    private function getBoundaries(array $parameters): array
    {
        $match = self::glueUpWithComma($parameters);
        $continuousParams = $this->getSpecificParams(
            $match,
            'continuous'
        );
        $continuous = [];
        foreach ($continuousParams as $attribute) {
            $column = "
SELECT max(C.content)
FROM essence E
        JOIN essence_thing ET
             ON E.id = ET.essence_id
        JOIN content C
             ON ET.thing_id = C.thing_id
        JOIN attribute A
             ON C.attribute_id = A.id
WHERE E.id = EE.id
 and A.code = '$attribute'
";
            $continuous["max@$attribute"] =
                "($column) AS \"max@$attribute\"";
            $column = "

SELECT min(C.content)
FROM essence E
        JOIN essence_thing ET
             ON E.id = ET.essence_id
        JOIN content C
             ON ET.thing_id = C.thing_id
        JOIN attribute A
             ON C.attribute_id = A.id
WHERE E.id = EE.id
 and A.code = '$attribute'
";
            $continuous["min@$attribute"] =
                "($column) AS \"min@$attribute\"";
        }

        $selectPhase = implode(",", $continuous);
        $essence = $this->getSource()->getEssence();
        $getBoundaries = "
SELECT $selectPhase
FROM essence EE
where EE.code = '$essence';
";
        $isSuccess = !empty($selectPhase);
        if ($isSuccess) {
            $data = $this->readData($getBoundaries);
            $isSuccess = count($data) !== 0;
        }
        $boundaries = [];
        if ($isSuccess) {
            $boundaries = $data[0];
        }

        return $boundaries;
    }

    /**
     * @param array $parameters
     * @return array
     */
    private function getValues(array $parameters): array
    {
        $match = self::glueUpWithComma($parameters);
        $params = $this->getSpecificParams($match, 'discrete');
        $essence = $this->getSource()->getEssence();
        $discrete = [];
        foreach ($params as $attribute) {
            $column = "
SELECT
    DISTINCT C.content
FROM essence E
        JOIN essence_thing ET
             ON E.id = ET.essence_id
        JOIN content C
             ON ET.thing_id = C.thing_id
        JOIN attribute A
             ON C.attribute_id = A.id
WHERE E.code = '$essence'
 and A.code = '$attribute'
ORDER BY C.content
";
            $discrete[$attribute] = $column;
        }

        $values = [];
        foreach ($discrete as $attribute => $readValues) {
            $content = $this->readData($readValues);
            $isSuccess = count($content) !== 0;
            if ($isSuccess) {
                $simplified = array_column($content, 'content');
                $values[$attribute] = $simplified;
            }
        }
        return $values;
    }
}
