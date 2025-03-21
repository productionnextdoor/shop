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
 */
class AttributeOption
{
    /**
     * @param string $value
     * @param LocaleText[] $label
     */
    public function __construct(
        private readonly string $value,
        private readonly array  $label
    ) {}

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return LocaleText[]
     */
    public function getLabel(): array
    {
        return $this->label;
    }
}
