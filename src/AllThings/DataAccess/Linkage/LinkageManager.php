<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 14:22
 */

namespace AllThings\DataAccess\Linkage;

use PDO;

class LinkageManager implements ILinkageManager
{
    protected ILinkageHandler $linkageHandler;

    /**
     * @param PDO           $db
     * @param ILinkageTable $table
     * @param ForeignKey    $leftKey
     * @param ForeignKey    $rightKey
     */
    public function __construct(
        PDO $db,
        ILinkageTable $table,
        ForeignKey $leftKey,
        ForeignKey $rightKey
    ) {
        $this->linkageHandler = new LinkageHandler(
            $leftKey,
            $rightKey,
            $table,
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
