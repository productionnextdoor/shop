<?php declare(strict_types=1);

namespace PmLimitBasket;

use PmAbstract\Bootstrap\Helper\Attribute;
use PmAbstract\Bootstrap\Helper\AttributeSet;
use PmAbstract\Bootstrap\Helper\EntityRelation;
use PmAbstract\Bootstrap\Helper\LocaleText;
use PmAbstract\Components\Plugin\PmAbstractPlugin;
use Shopware\Core\Checkout\Order\OrderDefinition;
use Shopware\Core\System\CustomField\CustomFieldTypes;

class PmLimitBasket extends PmAbstractPlugin
{
    /**
     * {@inheritDoc}
     */
    public function getAttributes(): iterable
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getMails(): iterable
    {
        return [];
    }
}
