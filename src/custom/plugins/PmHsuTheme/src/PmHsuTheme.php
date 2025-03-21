<?php declare(strict_types=1);

namespace PmHsuTheme;

use PmAbstract\Components\Plugin\PmAbstractPlugin;
use Shopware\Storefront\Framework\ThemeInterface;

class PmHsuTheme extends PmAbstractPlugin implements ThemeInterface
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
