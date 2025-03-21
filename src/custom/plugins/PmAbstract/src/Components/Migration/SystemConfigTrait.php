<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: dennis
 * Date: 16.11.22
 * Time: 02:52
 */

namespace PmAbstract\Components\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SystemConfig\Exception\InvalidSettingValueException;

trait SystemConfigTrait
{
    /**
     * Get the value of a configuration by its key and optionally the sales channel id.
     *
     * @param Connection $connection
     * @param string $key
     * @param string|null $salesChannelId
     * @return array<mixed>|bool|float|int|string|null
     * @throws \Doctrine\DBAL\Exception
     * @throws \JsonException
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    protected function getConfiguration(Connection $connection, string $key, string $salesChannelId = null)
    {
        $salesChannelClause = $this->assembleSalesChannelWhereClause($salesChannelId);

        $query = <<<SQL
            SELECT `configuration_value`
            FROM `system_config`
            WHERE `configuration_key` = :configurationKey
                AND ${salesChannelClause}
        SQL;

        $query .= ' AND `sales_channel_id` ' . ($salesChannelId !== null ? '= :salesChannelId' : 'IS NULL');

        $configurationValue = $connection->executeQuery($query, [
            'salesChannelId' => $salesChannelId,
            'configurationKey' => $key,
        ])->fetchOne();

        if ($configurationValue === false) {
            return null;
        }

        $config = json_decode($configurationValue, true, 512, JSON_THROW_ON_ERROR);
        return $config['_value'];
    }

    /**
     * @param Connection $connection
     * @param string $key
     * @param string|null $salesChannelId
     * @return string
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \JsonException
     * @throws InvalidSettingValueException
     */
    protected function getStringConfiguration(Connection $connection, string $key, string $salesChannelId = null): string
    {
        $value = $this->getConfiguration($connection, $key, $salesChannelId);
        if (!\is_array($value)) {
            return (string) $value;
        }

        throw new InvalidSettingValueException($key, 'string', \gettype($value));
    }

    /**
     * @param Connection $connection
     * @param string $key
     * @param string|null $salesChannelId
     * @return int
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \JsonException
     * @throws InvalidSettingValueException
     */
    protected function getIntConfiguration(Connection $connection, string $key, string $salesChannelId = null): int
    {
        $value = $this->getConfiguration($connection, $key, $salesChannelId);
        if (!\is_array($value)) {
            return (int) $value;
        }

        throw new InvalidSettingValueException($key, 'int', \gettype($value));
    }

    /**
     * @param Connection $connection
     * @param string $key
     * @param string|null $salesChannelId
     * @return float
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \JsonException
     * @throws InvalidSettingValueException
     */
    protected function getFloatConfiguration(Connection $connection, string $key, string $salesChannelId = null): float
    {
        $value = $this->getConfiguration($connection, $key, $salesChannelId);
        if (!\is_array($value)) {
            return (float) $value;
        }

        throw new InvalidSettingValueException($key, 'float', \gettype($value));
    }

    /**
     * @param Connection $connection
     * @param string $key
     * @param string|null $salesChannelId
     * @return bool
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \JsonException
     * @SuppressWarnings(PHPMD.BooleanGetMethodName) - this is a getter...
     */
    protected function getBoolConfiguration(Connection $connection, string $key, string $salesChannelId = null): bool
    {
        return (bool) $this->getConfiguration($connection, $key, $salesChannelId);
    }

    /**
     * Add or create the value of a system config variable by its key and optionally the sales channel id
     *
     * @param Connection $connection
     * @param string $key
     * @param array<mixed>|bool|float|int|string|null $value
     * @param string|null $salesChannelId
     * @return void
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \JsonException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function setConfiguration(Connection $connection, string $key, $value, string $salesChannelId = null): void
    {
        $salesChannelClause = $this->assembleSalesChannelWhereClause($salesChannelId);

        // check if the config key exists
        $query = <<<SQL
            SELECT LOWER(HEX(`id`))
            FROM `system_config`
            WHERE `configuration_key` = :key
                AND ${salesChannelClause}
        SQL;

        $configId = $connection->executeQuery($query, [
            'key' => $key,
            'salesChannelId' => $salesChannelId,
        ])->fetchOne();

        $configurationValue = json_encode(array('_value' => $value), JSON_THROW_ON_ERROR);

        if ($configId) {
            // update the config value and update the updated_at timestamp
            $query = <<<SQL
                UPDATE `system_config`
                SET `configuration_value` = :value,
                    `updated_at` = :updatedAt
                WHERE `id` = UNHEX(:id)
            SQL;

            $connection->executeStatement($query, [
                'value' => $configurationValue,
                'updatedAt' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'id' => $configId,
            ]);
            return;
        }

        // insert the config value
        $query = <<<SQL
            INSERT INTO `system_config` (`id`, `configuration_key`, `configuration_value`, `sales_channel_id`, `created_at`)
            VALUES (UNHEX(:id), :key, :value, UNHEX(:salesChannelId), :createdAt)
        SQL;

        $connection->executeStatement($query, [
            'id' => Uuid::randomBytes(),
            'key' => $key,
            'value' => $configurationValue,
            'salesChannelId' => $salesChannelId,
            'createdAt' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);
    }

    /**
     * @param string|null $salesChannelId
     * @return string
     */
    private function assembleSalesChannelWhereClause(?string $salesChannelId): string
    {
        if (empty($salesChannelId)) {
            return '`sales_channel_id` IS NULL';
        }

        return '`sales_channel_id` = UNHEX(:salesChannelId)';
    }
}
