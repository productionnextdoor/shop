<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: dennis
 * Date: 23.06.22
 * Time: 16:27
 */

namespace PmAbstract\Bootstrap\Helper;

class AttributeSet
{
    /**
     * @param string $name
     * @param LocaleText[] $label
     * @param EntityRelation[] $relations
     * @param iterable{Attribute} $attributes
     */
    public function __construct(
        private readonly string   $name,
        private readonly array    $label,
        private readonly array    $relations,
        private readonly iterable $attributes
    ) {}

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return LocaleText[]
     */
    public function getLabel(): array
    {
        return $this->label;
    }

    /**
     * @return EntityRelation[]
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

    /**
     * @return iterable{Attribute}
     */
    public function getAttributes(): iterable
    {
        return $this->attributes;
    }
}
