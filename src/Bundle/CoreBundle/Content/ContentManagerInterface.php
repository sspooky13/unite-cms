<?php


namespace UniteCMS\CoreBundle\Content;

use UniteCMS\CoreBundle\Domain\Domain;
use UniteCMS\CoreBundle\Exception\InvalidContentVersionException;
use UniteCMS\CoreBundle\Query\QueryCriteria;

interface ContentManagerInterface
{
    public function find(Domain $domain, string $type, QueryCriteria $criteria, bool $includeDeleted = false, ?callable $resultFilter = null) : ContentResultInterface;
    public function get(Domain $domain, string $type, string $id, bool $includeDeleted = false) : ?ContentInterface;

    public function create(Domain $domain, string $type) : ContentInterface;

    public function update(Domain $domain, ContentInterface $content, array $inputData = []) : ContentInterface;

    /**
     * @param Domain $domain
     * @param ContentInterface $content
     * @param int $version
     *
     * @return ContentInterface
     *
     * @throws InvalidContentVersionException
     */
    public function revert(Domain $domain, ContentInterface $content, int $version) : ContentInterface;

    /**
     * @param Domain $domain
     * @param ContentInterface $content
     * @param int $limit
     *
     * @return ContentRevisionInterface[]
     */
    public function revisions(Domain $domain, ContentInterface $content, int $limit = 20) : array;

    public function delete(Domain $domain, ContentInterface $content) : ContentInterface;
    public function recover(Domain $domain, ContentInterface $content) : ContentInterface;

    public function persist(Domain $domain, ContentInterface $content, string $persistType) : void;
}
