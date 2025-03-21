<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: dennis
 * Date: 23.06.22
 * Time: 15:43
 */

namespace PmAbstract\Components\Plugin;

use PmAbstract\Bootstrap\Attributes;
use PmAbstract\Bootstrap\DatabaseChangeInterface;
use PmAbstract\Bootstrap\Mails;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;

abstract class PmAbstractPlugin extends Plugin implements PmAbstractPluginInterface
{
    /**
     * @param InstallContext $installContext
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) - Shopware Class
     */
    public function install(InstallContext $installContext): void
    {
        foreach ($this->getDatabaseChanges() as $databaseChange) {
            $databaseChange->update();
        }
    }

    public function update(UpdateContext $updateContext): void
    {
        $this->install($updateContext);
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            return;
        }

        foreach ($this->getDatabaseChanges() as $databaseChange) {
            $databaseChange->remove();
        }
    }

    /**
     * @return iterable{DatabaseChangeInterface}
     */
    private function getDatabaseChanges(): iterable
    {
        yield new Attributes($this->container, $this->getNamespace(), $this->getAttributes());
        yield new Mails($this->container, $this->getNamespace(), $this->getMails());
    }
}
