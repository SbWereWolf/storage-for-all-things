<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 20:27
 */

namespace AllThings\DataAccess\Handler;


use AllThings\DataAccess\Implementation\EssenceLocation;
use AllThings\DataAccess\Implementation\EssenceSource;
use AllThings\Essence\Essence;
use AllThings\Essence\IEssence;

class EssenceRecordHandler implements Valuable, Hideable, Retrievable
{

    private $storageLocation = 'essence';
    private $dataSource = 'essence';

    /**
     * @var \PDO
     */
    private $dataPath;
    private $essence = null;

    function __construct(IEssence $essence, \PDO $dataPath)
    {
        $this->essence = $essence;
        $this->dataPath = $dataPath;
    }

    function add(string $code): bool
    {
        $essence = $this->setEssenceByCode($code);

        $result = ($this->getEssenceLocation())->insert($essence);

        $this->setEssence($result, $essence);

        return $result;

    }

    /**
     * @param string $code
     * @return IEssence
     */
    private function setEssenceByCode(string $code): IEssence
    {
        $essence = Essence::GetDefaultEssence();
        $essence->setCode($code);

        return $essence;
    }

    private function getEssenceLocation(): EssenceLocation
    {
        $storageLocation = new EssenceLocation($this->storageLocation, $this->dataPath);

        return $storageLocation;
    }

    /**
     * @param $result
     * @param $essence
     */
    private function setEssence(bool $result, IEssence $essence): void
    {
        if ($result) {
            $this->essence = $essence;
        }
        if (!$result) {
            $this->essence = null;
        }
    }

    function hide(string $code): bool
    {
        $essence = $this->setEssenceByCode($code);

        $result = ($this->getEssenceLocation())->setIsHidden($essence);

        $this->setEssence($result, $essence);

        return $result;

    }

    function write(string $code): bool
    {
        $essence = $this->setEssenceByCode($code);

        $resultData = Essence::GetDefaultEssence();

        $result = ($this->getEssenceLocation())->update($essence, $resultData);

        $this->setEssence($result, $resultData);

        return $result;

    }

    function read(string $code): bool
    {
        $essence = $this->setEssenceByCode($code);

        $result = ($this->getEssenceSource())->select($essence);

        $this->setEssence($result, $essence);

        return $result;

    }

    private function getEssenceSource(): EssenceSource
    {

        $repository = new EssenceSource($this->dataSource, $this->dataPath);
        return $repository;
    }

    function retrieveData(): IEssence
    {
        $essence = $this->essence->GetEssenceCopy();

        return $essence;
    }
}
