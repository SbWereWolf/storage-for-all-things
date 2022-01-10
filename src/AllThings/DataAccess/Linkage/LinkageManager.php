<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 10.01.2022, 6:49
 */

namespace AllThings\DataAccess\Linkage;

use PDO;

class LinkageManager implements ILinkageManager
{
    protected ILinkageHandler $linkageHandler;

    public function __construct(
        PDO $db,
        ILinkageTable $location,
        ForeignKey $leftKey,
        ForeignKey $rightKey
    ) {
        $this->linkageHandler = new LinkageHandler(
            $leftKey,
            $rightKey,
            $location,
            $db,
        );
    }

    public function attach(ILinkage $linkage): bool
    {
        $result = $this->linkageHandler->combine($linkage);

        return $result;
    }

    public function detach(ILinkage $linkage): bool
    {
        $result = $this->linkageHandler->split($linkage);

        return $result;
    }

    public function getAssociated(ILinkage $linkage): bool
    {
        $result = $this->linkageHandler->getRelated($linkage);

        return $result;
    }

    public function retrieveData(): array
    {
        return $this->linkageHandler->retrieveData();
    }

    public function has(): bool
    {
        return $this->linkageHandler->has();
    }
}
