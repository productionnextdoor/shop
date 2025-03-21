<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: dennis
 * Date: 23.06.22
 * Time: 16:27
 */

namespace PmAbstract\Bootstrap\Helper;

/**
 * Class Attribute
 *
 * @package PmAbstract\Bootstrap\Helper
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class EntityRelation
{
    /**
     * @param string $entityName
     * @param string|null $id
     */
    public function __construct(
        private readonly string $entityName,
        private readonly ?string $id = null
    ) {}

    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return $this->entityName;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }
}
