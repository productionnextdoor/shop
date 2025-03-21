<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: dennis
 * Date: 16.11.22
 * Time: 11:10
 */

namespace PmAbstract\Bootstrap\Helper;

class LocaleText
{
    /**
     * @param string $locale
     * @param string $text
     */
    public function __construct(
        private readonly string $locale,
        private readonly string $text
    ) {}

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
