<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: dennis
 * Date: 16.11.22
 * Time: 11:37
 */

namespace PmAbstract\Bootstrap\Helper;

trait LocaleTrait
{
    public function convertLocaleToAssocArray(array $locales): array
    {
        $result = [];
        foreach ($locales as $locale) {
            $result[$locale->getLocale()] = $locale->getText();
        }
        return $result;
    }
}
