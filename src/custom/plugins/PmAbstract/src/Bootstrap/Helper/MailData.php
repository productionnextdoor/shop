<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: dennis
 * Date: 18.09.22
 * Time: 23:56
 */

namespace PmAbstract\Bootstrap\Helper;

class MailData
{

    /**
     * @param string $name
     * @param LocaleText[] $subject
     * @param LocaleText[] $senderName
     * @param LocaleText[] $contentPlain
     * @param LocaleText[] $contentHtml
     * @param string[] $availableEntities
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList) - Data Struct
     */
    public function __construct(
        private readonly string $name,
        private readonly array  $subject,
        private readonly array  $senderName,
        private readonly array  $contentPlain,
        private readonly array  $contentHtml,
        private readonly array  $availableEntities
    ) {}

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return LocaleText[]
     */
    public function getSubject(): array
    {
        return $this->subject;
    }

    /**
     * @return LocaleText[]
     */
    public function getSenderName(): array
    {
        return $this->senderName;
    }

    /**
     * @return LocaleText[]
     */
    public function getContentPlain(): array
    {
        return $this->contentPlain;
    }

    /**
     * @return LocaleText[]
     */
    public function getContentHtml(): array
    {
        return $this->contentHtml;
    }

    /**
     * @return string[]
     */
    public function getAvailableEntities(): array
    {
        return $this->availableEntities;
    }
}
