<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: dennis
 * Date: 23.06.22
 * Time: 16:25
 */

namespace PmAbstract\Components\Plugin;

use PmAbstract\Bootstrap\Helper\Attribute;
use PmAbstract\Bootstrap\Helper\MailData;

interface PmAbstractPluginInterface
{
    /**
     * Returns an iterable of all the attributes that should be created.
     * @return iterable<Attribute>
     * @see Attribute
     */
    public function getAttributes(): iterable;

    /**
     * Returns an iterable of all the mails that should be created.
     * @return iterable<MailData>
     * @see MailData
     */
    public function getMails(): iterable;
}
