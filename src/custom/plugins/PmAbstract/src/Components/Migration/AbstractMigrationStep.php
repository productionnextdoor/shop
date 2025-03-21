<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: dennis
 * Date: 01.09.22
 * Time: 16:08
 */

namespace PmAbstract\Components\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

abstract class AbstractMigrationStep extends MigrationStep
{
    /**
     * @param Connection $connection
     * @param string $tableName
     * @param string $columnName
     * @return bool
     */
    protected function databaseColumnExists(Connection $connection, string $tableName, string $columnName): bool
    {
        $query = <<<SQL
            SELECT COLUMN_NAME
            FROM information_schema.columns
            WHERE TABLE_SCHEMA = :database
                AND TABLE_NAME = :tableName
                AND COLUMN_NAME = :columnName
        SQL;

        $columnNameInDb = $connection->executeQuery($query, [
            'database' => $connection->getDatabase(),
            'tableName' => $tableName,
            'columnName' => $columnName,
        ])->fetchOne();

        return $columnNameInDb === $columnName;
    }
}
