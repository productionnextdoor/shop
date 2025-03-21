<?php declare(strict_types=1);

namespace PmCustomFields;

use PmAbstract\Bootstrap\Helper\Attribute;
use PmAbstract\Bootstrap\Helper\AttributeSet;
use PmAbstract\Bootstrap\Helper\EntityRelation;
use PmAbstract\Bootstrap\Helper\LocaleText;
use PmAbstract\Components\Plugin\PmAbstractPlugin;
use Shopware\Core\Checkout\Order\OrderDefinition;
use Shopware\Core\System\CustomField\CustomFieldTypes;

class PmCustomFields extends PmAbstractPlugin
{
    /**
     * {@inheritDoc}
     */
    public function getAttributes(): iterable
    {
        yield new AttributeSet(
            'order',
            [
                new LocaleText('en-GB', 'Order'),
                new LocaleText('de-DE', 'Bestellung')
            ],
            [ new EntityRelation(OrderDefinition::ENTITY_NAME) ],
            [
                (new Attribute(
                    'Exported',
                    [
                        new LocaleText('en-GB', 'Exported'),
                        new LocaleText('de-DE', 'Exportiert')
                    ],
                    CustomFieldTypes::BOOL,
                    [
                        new LocaleText('en-GB', 'Order has been exported to the HSU platform'),
                        new LocaleText('de-DE', 'Bestellung wurde in die HSU-Plattform exportiert')
                    ]
                ))->setComponent('sw-field'),
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getMails(): iterable
    {
        return [];
    }
}
