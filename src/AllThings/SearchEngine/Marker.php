<?php

namespace AllThings\SearchEngine;

use AllThings\StorageEngine\Installation;
use PDO;

class Marker
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
     * @param array $parameters
     * @return array
     */
    public function getBoundaries(array $parameters): array
    {
        $params = $this->getSpecificParams('continuous', $parameters);
        $continuous = [];
        $filters = [];
        foreach ($params as $attribute) {
            $max = "max@$attribute";
            $min = "min@$attribute";
            $filters[$attribute] = ['max' => $max, 'min' => $min];
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
            $continuous[$max] =
                "($column) AS \"$max\"";
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
            $continuous[$min] =
                "($column) AS \"$min\"";
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
        $confines = [];
        foreach ($filters as $attribute => $locality) {
            $max = '';
            $min = '';
            foreach ($locality as $boundary => $key) {
                if ($boundary === 'max') {
                    $max = $boundaries[$key];
                }
                if ($boundary === 'min') {
                    $min = $boundaries[$key];
                }
            }

            $filter = new ContinuousFilter($attribute, $max, $min);
            $confines[] = $filter;
        }

        return $confines;
    }

    /**
     * @param string $rangeType
     * @param array $parameters
     * @return array
     */
    private function getSpecificParams(
        string $rangeType,
        array  $parameters
    ): array
    {
        $match = '';
        $isSuccess = count($parameters) !== 0;
        if ($isSuccess) {
            $match .= '\'';
            $match .= implode("','", $parameters);
            $match .= '\'';
        }
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
     * @return Installation
     */
    private function getSource(): Installation
    {
        return $this->source;
    }

    /**
     * @param array $parameters
     * @return array
     */
    public function getEnumerations(array $parameters): array
    {
        $params = $this->getSpecificParams('discrete', $parameters);
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

        $enumerations = [];
        foreach ($discrete as $attribute => $readValues) {
            $content = $this->readData($readValues);
            $isSuccess = count($content) !== 0;
            if ($isSuccess) {
                $simplified = array_column($content, 'content');
                $filter = new DiscreteFilter($attribute, $simplified);
                $enumerations[] = $filter;
            }
        }
        return $enumerations;
    }
}
