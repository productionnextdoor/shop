<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: dennis
 * Date: 23.06.22
 * Time: 17:28
 */

namespace PmAbstract\Bootstrap;

use PmAbstract\Bootstrap\Helper\Attribute;
use PmAbstract\Bootstrap\Helper\AttributeSet;
use PmAbstract\Bootstrap\Helper\EntityRelation;
use PmAbstract\Bootstrap\Helper\LocaleTrait;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\CustomField\Aggregate\CustomFieldSet\CustomFieldSetDefinition;
use Shopware\Core\System\CustomField\Aggregate\CustomFieldSet\CustomFieldSetEntity;
use Shopware\Core\System\CustomField\CustomFieldDefinition;
use Shopware\Core\System\CustomField\CustomFieldEntity;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Attributes extends AbstractDatabaseChange
{
    use LocaleTrait;

    private EntityRepository $customFieldSetRepository;

    private EntityRepository $customFieldRepository;

    /**
     * Attributes constructor.
     *
     * @param ContainerInterface $container
     * @param string $namespace
     * @param iterable{AttributeSet} $attributes
     */
    public function __construct(
        ContainerInterface $container,
        string $namespace,
        private readonly iterable $attributes
    ) {
        parent::__construct($container, $namespace);

        $this->customFieldSetRepository = $this->getRepository(CustomFieldSetDefinition::ENTITY_NAME);
        $this->customFieldRepository = $this->getRepository(CustomFieldDefinition::ENTITY_NAME);
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        /** @var AttributeSet $attributeSet */
        foreach ($this->attributes as $attributeSet) {
            $customFieldSetName = $attributeSet->getName();
            $customFieldSet = $this->getCustomFieldSet($customFieldSetName);

            if (!$customFieldSet) {
                $this->createCustomFieldSet($attributeSet);
            }

            foreach ($attributeSet->getAttributes() as $attribute) {
                $customField = $this->getCustomField($customFieldSetName, $attribute->getName());

                if (!$customField) {
                    $this->createCustomField($customFieldSetName, $attribute);
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function remove(): void
    {
        $criteria = (new Criteria())
            ->addFilter(new ContainsFilter('name', $this->getCustomFieldName()));

        $customFields = $this->customFieldRepository->search($criteria, $this->context);

        if ($customFields) {
            foreach ($customFields as $customField) {
                $this->removeCustomField($customField->getId());
            }
        }

        $criteria = (new Criteria())
            ->addFilter(new ContainsFilter('name', $this->getCustomFieldSetName()));

        $customFieldSets = $this->customFieldSetRepository->search($criteria, $this->context);

        foreach ($customFieldSets as $customFieldSet) {
            $this->removeCustomFieldset($customFieldSet->getId());
        }
    }

    private function getCustomFieldSet(string $name): ?CustomFieldSetEntity
    {
        if (!$name) {
            return null;
        }

        $criteria = (new Criteria())
            ->addFilter(new EqualsFilter('name', $this->getCustomFieldSetName($name)));

        return $this->customFieldSetRepository
            ->search($criteria, $this->context)
            ->first();
    }

    private function createCustomFieldSet(AttributeSet $attributeSet): void
    {
        $relationsData = array_map(static function (EntityRelation $relation) {
            $data = [
                'entityName' => $relation->getEntityName(),
            ];

            if ($relation->getId()) {
                $data['id'] = $relation->getId();
            }

            return $data;
        }, $attributeSet->getRelations());

        $this->customFieldSetRepository->create([
            [
                'id' => $this->randomHex(),
                'name' => $this->getCustomFieldSetName($attributeSet->getName()),
                'config' => [
                    'label' => $this->convertLocaleToAssocArray($attributeSet->getLabel()),
                ],
                'relations' => $relationsData,
            ]
        ], $this->context);
    }

    private function removeCustomFieldset(string $customFieldSetId): void
    {
        $this->customFieldSetRepository->delete([
            ['id' => $customFieldSetId]
        ], $this->context);
    }

    private function getCustomField(string $customFieldSetName, string $name): ?CustomFieldEntity
    {
        if (!$customFieldSetName || !$name) {
            return null;
        }

        $customFieldSet = $this->getCustomFieldSet($customFieldSetName);

        if (!$customFieldSet) {
            return null;
        }

        $criteria = (new Criteria())
            ->addFilter(new EqualsFilter('name', $this->getCustomFieldName($name)))
            ->addFilter(new EqualsFilter('customFieldSetId', $customFieldSet->getId()));

        return $this->customFieldRepository
            ->search($criteria, $this->context)
            ->first();
    }

    private function createCustomField(string $customFieldSetName, Attribute $customField): void
    {
        $customFieldSet = $this->getCustomFieldSet($customFieldSetName);

        if (!$customFieldSet) {
            return;
        }

        $customFieldConfigOptions = null;
        foreach ($customField->getOptions() as $option) {
            $customFieldConfigOptions[] = [
                'value' => $option->getValue(),
                'label' => $this->convertLocaleToAssocArray($option->getLabel()),
            ];
        }

        $customFieldConfig = [
            'componentName' => $customField->getComponent(),
            'customFieldPosition' => $customField->getPosition(),
            'customFieldType' => $customField->getFieldType(),
            'label' => $this->convertLocaleToAssocArray($customField->getLabel()),
            'helpText' => $this->convertLocaleToAssocArray($customField->getHelpText()),
            'placeholder' => $this->convertLocaleToAssocArray($customField->getPlaceholder()),
            'options' => $customFieldConfigOptions
        ];

        $this->customFieldRepository->create([
            [
                'id' => $this->randomHex(),
                'name' => $this->getCustomFieldName($customField->getName()),
                'type' => $customField->getType(),
                'config' => array_filter($customFieldConfig, static fn($value) => !is_null($value)),
                'customFieldSetId' => $customFieldSet->getId()
            ]
        ], $this->context);
    }

    private function removeCustomField(string $customFieldId): void
    {
        $this->customFieldRepository->delete([
            ['id' => $customFieldId]
        ], $this->context);
    }

    private function getCustomFieldSetName(string $entityName = ''): string
    {
        return sprintf(
            '%s_field_set__%s',
            $this->toSnakeCase($this->namespace),
            $this->toSnakeCase($entityName)
        );
    }

    private function getCustomFieldName(string $entityName = ''): string
    {
        return sprintf(
            '%s_field__%s',
            $this->toSnakeCase($this->namespace),
            $this->toSnakeCase($entityName)
        );
    }
}
