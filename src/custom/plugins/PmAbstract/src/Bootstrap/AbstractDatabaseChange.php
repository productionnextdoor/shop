<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: dennis
 * Date: 23.06.22
 * Time: 18:46
 */

namespace PmAbstract\Bootstrap;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractDatabaseChange implements DatabaseChangeInterface
{
    protected readonly Context $context;

    /**
     * @param ContainerInterface $container
     * @param string $namespace
     * @SuppressWarnings(PHPMD.StaticAccess) - Shopware createDefaultContext is static
     */
    public function __construct(
        public readonly ContainerInterface $container,
        public readonly string $namespace
    ) {
        $this->context = Context::createDefaultContext();
    }

    /**
     * Helper function to get a repository for an entity by its name.
     *
     * @param string $entityName
     * @return EntityRepository
     */
    protected function getRepository(string $entityName): EntityRepository
    {
        /** @var EntityRepository $repo */
        $repo = $this->container->get("{$entityName}.repository");
        return $repo;
    }

    /**
     * Helper function to get a random Hexadecimal UUID
     *
     * @return string
     * @SuppressWarnings(PHPMD.StaticAccess) - Shopware UUID is static
     */
    protected function randomHex(): string
    {
        return Uuid::randomHex();
    }

    /**
     * Helper function to switch the case of a string to snake case.
     * This is mainly used to generate technical names that are used in the database.
     *
     * @param string $string
     * @return string
     */
    protected function toSnakeCase(string $string): string
    {
        // convert CamelCase to snake_case (e.g. "MyCustomField" -> "my_custom_field")
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $string));
    }
}