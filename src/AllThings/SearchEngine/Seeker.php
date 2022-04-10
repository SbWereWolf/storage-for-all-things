<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 4/10/22, 2:45 PM
 */

namespace AllThings\SearchEngine;

use AllThings\Blueprint\Relation\BlueprintFactory;
use AllThings\StorageEngine\Installation;
use Exception;
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
     * @throws Exception
     */
    public function limits(): array
    {
        $marker = new Marker($this->getStorage());
        $parameters = $this->getParams([
            Searchable::DATA_TYPE_FIELD,
            Searchable::RANGE_TYPE_FIELD,
        ]);
        $continuous = [];
        $discrete = [];
        foreach ($parameters as $attribute => $settings) {
            $dataType = $settings[Searchable::DATA_TYPE_FIELD];
            $searchType = $settings[Searchable::RANGE_TYPE_FIELD];

            if ($searchType === Searchable::CONTINUOUS) {
                $continuous[] = new Filter($attribute, $dataType);
            }
            if ($searchType === Searchable::DISCRETE) {
                $discrete[] = new Filter($attribute, $dataType);
            }
        }
        $filters = $marker->getBoundaries($continuous);
        $result = $marker->getEnumerations($discrete);

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = array_merge($result, $filters);

        return $result;
    }

    /**
     * @throws Exception
     */
    public function seek(array $limits = []): array
    {
        $name = $this->getStorage()->name();
        $obtain = "SELECT * from $name";

        $isExists = !empty($limits);
        $where = [];
        if ($isExists) {
            foreach ($limits as $filter) {
                /* @var Filter $filter */
                $attribute = $filter->getAttribute();
                $format = Converter::getFieldFormat(
                    $filter->getDataType()
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

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $data = $this->readData($obtain);

        return $data;
    }

    public function getParams(array $fields): array
    {
        $store = $this->getStorage();
        $essence = $store->getEssence();
        $db = $store->getDb();

        $blueprint = (new BlueprintFactory($db))->make($essence);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $attributes = $blueprint->list($fields);

        return $attributes;
    }

    /**
     * @return Installation
     */
    private function getStorage(): Installation
    {
        return $this->source;
    }

    /**
     * @param Installation $source
     *
     * @return static
     * @noinspection PhpReturnValueOfMethodIsNeverUsedInspection
     */
    private function setSource(Installation $source): static
    {
        $this->source = $source;

        return $this;
    }

    private function readData(string $sql): array
    {
        $cursor = $this
            ->getStorage()
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

}
