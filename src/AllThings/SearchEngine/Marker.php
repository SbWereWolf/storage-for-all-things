<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\SearchEngine;

use AllThings\StorageEngine\Installation;
use Exception;
use PDO;

class Marker
{
    /**
     * @var Installation
     */
    private Installation $source;

    public function __construct(Installation $source)
    {
        $this->source = $source;
    }

    /**
     * @param Filter[] $parameters
     *
     * @return array
     * @throws Exception
     */
    public function getBoundaries(array $parameters): array
    {
        $continuous = [];
        $filters = [];
        foreach ($parameters as $filter) {
            $attribute = $filter->getAttribute();
            /** @var Filter $filter */
            $max = "max@$attribute";
            $min = "min@$attribute";
            $filters[$attribute] =
                [
                    'max' => $max,
                    'min' => $min,
                    'type' => $filter->getDataType(),
                ];

            $continuous[$max] =
                "MAX(\"$attribute\") AS \"$max\"";
            $continuous[$min] =
                "MIN(\"$attribute\") AS \"$min\"";
        }
        $selectPhase = implode(",", $continuous);
        $table = $this->getSource()->name();

        $getBoundaries = "SELECT $selectPhase FROM $table";
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

            $filter = new ContinuousFilter($attribute, $locality['type'], $min, $max);
            $confines[] = $filter;
        }

        return $confines;
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

    /**
     * @return Installation
     */
    private function getSource(): Installation
    {
        return $this->source;
    }

    /**
     * @param array $parameters
     *
     * @return array
     * @throws Exception
     */
    public function getEnumerations(array $parameters): array
    {
        $discrete = [];
        foreach ($parameters as $filter) {
            /** @var Filter $filter $table */
            $table = $this->getSource()->name();

            $attribute = $filter->getAttribute();
            $column =
                "
SELECT \"$attribute\" FROM $table GROUP BY \"$attribute\"
";
            $discrete[$attribute] = [];
            $discrete[$attribute]['columns'] = $column;
            $discrete[$attribute]['type'] = $filter->getDataType();
        }

        $enumerations = [];
        foreach ($discrete as $attribute => $settings) {
            $readValues = $settings['columns'];
            $content = $this->readData($readValues);
            $isContain = count($content) !== 0;
            if ($isContain) {
                $simplified = array_column($content, $attribute);
                $filter = new DiscreteFilter(
                    $attribute,
                    $settings['type'],
                    $simplified,
                );
                $enumerations[] = $filter;
            }
        }
        return $enumerations;
    }
}
