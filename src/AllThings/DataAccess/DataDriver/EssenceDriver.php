<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 20:27
 */

namespace AllThings\DataAccess\Implementation;


use AllThings\DataAccess\Core\EssenceLocation;
use AllThings\DataAccess\Core\EssenceSource;
use AllThings\Essence\EssenceEntity;
use AllThings\Essence\IEssence;

class EssenceDriver implements Valuable, Hideable, Retrievable
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

    function insert(string $code): bool
    {
        $essence = $this->setEssenceByCode($code);

        $result = ($this->getEssenceLocation())->add($essence);

        $this->setEssence($result, $essence);

        return $result;

    }

    /**
     * @param string $code
     * @return IEssence
     */
    private function setEssenceByCode(string $code): IEssence
    {
        $essence = EssenceEntity::GetDefaultExemplar();
        $essence->setCode($code);

        return $essence;
    }

    private function getEssenceLocation(): EssenceLocation
    {

        $repository = new EssenceLocation($this->storageLocation, $this->dataPath);
        return $repository;
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

        $result = ($this->getEssenceLocation())->hide($essence);

        $this->setEssence($result, $essence);

        return $result;

    }

    function write(string $code): bool
    {
        $essence = $this->setEssenceByCode($code);

        $resultData = EssenceEntity::GetDefaultExemplar();

        $result = ($this->getEssenceLocation())->write($essence, $resultData);

        $this->setEssence($result, $resultData);

        return $result;

    }

    function read(string $code): bool
    {
        $essence = $this->setEssenceByCode($code);

        $result = ($this->getEssenceSource())->read($essence);

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
