<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: dennis
 * Date: 23.06.22
 * Time: 16:27
 */

namespace PmAbstract\Bootstrap\Helper;

class Attribute
{

    /**
     * @param string $name
     * @param LocaleText[] $label
     * @param string $type
     * @param LocaleText[] $helpText
     * @param LocaleText[] $placeholder
     * @param string|null $component
     * @param string|null $fieldType
     * @param int $position
     * @param AttributeOption[] $options
     * @SuppressWarnings(PHPMD.ExcessiveParameterList) - Data Class
     */
    public function __construct(
        private readonly string $name,
        private readonly array  $label,
        private readonly string $type,
        private array           $helpText = [],
        private array           $placeholder = [],
        private ?string         $component = null,
        private ?string         $fieldType = null,
        private int             $position = 100,
        private array           $options = []
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
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return LocaleText[]
     */
    public function getHelpText(): array
    {
        return $this->helpText;
    }

    /**
     * @param LocaleText[] $helpText
     * @return self
     */
    public function setHelpText(array $helpText): self
    {
        $this->helpText = $helpText;
        return $this;
    }

    /**
     * @return LocaleText[]
     */
    public function getPlaceholder(): array
    {
        return $this->placeholder;
    }

    /**
     * @param LocaleText[] $placeholder
     * @return self
     */
    public function setPlaceholder(array $placeholder): self
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getComponent(): ?string
    {
        return $this->component;
    }

    /**
     * @param string|null $component
     * @return self
     */
    public function setComponent(?string $component): self
    {
        $this->component = $component;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFieldType(): ?string
    {
        return $this->fieldType;
    }

    /**
     * @param string|null $fieldType
     * @return self
     */
    public function setFieldType(?string $fieldType): self
    {
        $this->fieldType = $fieldType;
        return $this;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     * @return self
     */
    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return AttributeOption[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param AttributeOption[] $options
     * @return self
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }
}
