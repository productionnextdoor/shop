<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: dennis
 * Date: 23.06.22
 * Time: 17:24
 */

namespace PmAbstract\Bootstrap;

/**
 * Every database change needs to provide these methods
 *
 * Interface DatabaseChangeInterface
 * @package PmAbstract\Bootstrap
 */
interface DatabaseChangeInterface
{
    /**
     * Creates the new data in the database.
     */
    public function update(): void;

    /**
     * Removes the database changes. The database should be as if this plugin has never been installed.
     */
    public function remove(): void;
}
