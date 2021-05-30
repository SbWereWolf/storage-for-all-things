<?php
/**
 * storage-for-all-things
 * Copyright © 2019 Volkhin Nikolay
 * 01.12.19 0:42
 */

namespace AllThings\SearchEngine;


use AllThings\DataObject\ContinuousFilter;
use AllThings\DataObject\DiscreteFilter;
use AllThings\Essence\EssenceAttributeManager;
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

        $linkToData = $this->getSource()->getLinkToData();
        $dataSet = $linkToData->query($obtain, PDO::FETCH_ASSOC);

        $data = [];
        if ($dataSet !== false) {
            $data = $dataSet->fetchAll();
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
        $essence = $this->getSource()->getEssence();
        $linkToData = $this->getSource()->getLinkToData();

        $manager = new EssenceAttributeManager($essence, '', $linkToData);
        $isSuccess = $manager->getAssociated();
        if ($isSuccess) {
            $isSuccess = $manager->has();
        }
        $match = '';
        if ($isSuccess) {
            $attributes = $manager->retrieveData();
            $match = implode("','", $attributes);
            $match = "'$match'";
        }

        $getValues = "
SELECT
    code,data_type,range_type
FROM
    attribute
WHERE 
      code IN ($match)
";
        $range = $linkToData->query($getValues, PDO::FETCH_ASSOC);

        $data = [];
        if ($range !== false) {
            $data = $range->fetchAll();
        }

        $continuous = [];
        $discrete = [];
        foreach ($data as $options) {
            $attribute = $options['code'];
            $type = $options['range_type'];
            switch ($type) {
                case 'continuous':
                    $column = "
SELECT
    max(C.content)
FROM
    attribute A
    JOIN content C
    ON C.attribute_id = A.id
    JOIN essence_thing ET 
    ON C.thing_id = ET.thing_id
WHERE
        A.code = '$attribute'
";
                    $continuous["max@$attribute"] =
                        "($column) AS \"max@$attribute\"";
                    $column = "
SELECT
    min(C.content)
FROM
    attribute A
    JOIN content C
    ON C.attribute_id = A.id
    JOIN essence_thing ET 
    ON C.thing_id = ET.thing_id
WHERE
        A.code = '$attribute'
";
                    $continuous["min@$attribute"] =
                        "($column) AS \"min@$attribute\"";
                    break;
                case 'discrete':
                    $column = "
SELECT
    DISTINCT C.content
FROM
    essence E 
    JOIN essence_attribute
    ON E.id = essence_attribute.essence_id
    JOIN  attribute A
    ON essence_attribute.attribute_id = A.id
    JOIN content C
    ON C.attribute_id = A.id
    JOIN essence_thing ET 
    ON C.thing_id = ET.thing_id
WHERE
        E.code = '$essence'  
    AND A.code = '$attribute'    
ORDER BY C.content
";
                    $discrete[$attribute] = $column;
                    break;
            }
        }

        $selectPhase = implode(",", $continuous);
        $getRange = "
SELECT
    $selectPhase
FROM
    essence E
WHERE 
    E.code = '$essence'
";
        $isSuccess = strlen($selectPhase) > 0;
        if ($isSuccess) {
            $cursor = $linkToData->query($getRange, PDO::FETCH_ASSOC);
            $isSuccess = $range !== false;
        }
        if ($isSuccess && isset($cursor)) {
            $range = $cursor->fetchAll();
            $isSuccess = count($range) !== 0;
        }

        $data = [];
        if ($isSuccess) {
            $data['continuous'] = $range[0];
        }

        foreach ($discrete as $attribute => $getValues) {
            $cursor = $linkToData->query($getValues, PDO::FETCH_ASSOC);
            $isSuccess = $cursor !== false;

            $values = null;
            if ($isSuccess) {
                $values = $cursor->fetchAll();
                $isSuccess = count($values) !== 0;
            }
            if ($isSuccess) {
                $data['discrete'][$attribute] = $values;
            }
        }

        $isSuccess = !empty($data['discrete']);
        if ($isSuccess) {
            foreach ($data['discrete'] as $key => $values) {
                $simplified = array_column($values, 'content');
                $data['discrete'][$key] = [];
                foreach ($simplified as $value) {
                    $data['discrete'][$key][] = $value;
                }
            }
        }


        return $data;
    }
}
