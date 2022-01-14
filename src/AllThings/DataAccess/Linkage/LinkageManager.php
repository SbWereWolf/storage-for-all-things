<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 14.01.2022, 6:19
 */

namespace AllThings\DataAccess\Linkage;

use PDO;

class LinkageManager implements ILinkageManager
{
    protected ILinkageHandler $linkageHandler;

    /**
     * @param PDO           $db
     * @param ILinkageTable $table
     */
    public function __construct(
        PDO $db,
        ILinkageTable $table
    ) {
        $this->linkageHandler = new LinkageHandler(
            $db,
            $table,
        );
    }

    public function attach(ILinkage $linkage): bool
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $this->linkageHandler->combine($linkage);

        return $result;
    }

    public function detach(ILinkage $linkage): bool
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $this->linkageHandler->split($linkage);

        return $result;
    }

    public function getAssociated(ILinkage $linkage): array
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $this->linkageHandler->getRelated($linkage);

        return $result;
    }
}
