<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: dennis
 * Date: 19.09.22
 * Time: 00:14
 */

namespace PmAbstract\Bootstrap;

use PmAbstract\Bootstrap\Helper\LocaleTrait;
use PmAbstract\Bootstrap\Helper\MailData;
use Shopware\Core\Content\MailTemplate\Aggregate\MailTemplateType\MailTemplateTypeDefinition;
use Shopware\Core\Content\MailTemplate\MailTemplateDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Mails extends AbstractDatabaseChange
{
    use LocaleTrait;

    private EntityRepository $mailTemplateTypeRepository;

    private EntityRepository $mailTemplateRepository;

    /** @var iterable{MailData} */
    private iterable $mails;

    /**
     * Attributes constructor.
     *
     * @param ContainerInterface $container
     * @param string $namespace
     * @param iterable{MailData} $attributes
     */
    public function __construct(
        ContainerInterface $container,
        string             $namespace,
        iterable           $mails
    )
    {
        parent::__construct($container, $namespace);

        $this->mailTemplateTypeRepository = $this->getRepository(MailTemplateTypeDefinition::ENTITY_NAME);
        $this->mailTemplateRepository = $this->getRepository(MailTemplateDefinition::ENTITY_NAME);
        $this->mails = $mails;
    }

    public function update(): void
    {
        /** @var MailData $mail */
        foreach ($this->mails as $mail) {
            $technicalName = $this->toSnakeCase($mail->getName());

            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('technicalName', $technicalName));

            if ($this->mailTemplateTypeRepository->searchIds($criteria, $this->context)->firstId()) {
                continue;
            }

            $mailTemplateTypeId = $this->randomHex();

            $mailTemplateType = [
                [
                    'id' => $mailTemplateTypeId,
                    'name' => $mail->getName(),
                    'technicalName' => $technicalName,
                    'availableEntities' => $mail->getAvailableEntities()
                ],
            ];
            $this->mailTemplateTypeRepository->create($mailTemplateType, $this->context);

            $mailTemplate = [
                    [
                    'id' => $this->randomHex(),
                    'mailTemplateTypeId' => $mailTemplateTypeId,
                    'subject' => $this->convertLocaleToAssocArray($mail->getSubject()),
                    'senderName' => $this->convertLocaleToAssocArray($mail->getSenderName()),
                    'contentPlain' => $this->convertLocaleToAssocArray($mail->getContentPlain()),
                    'contentHtml' => $this->convertLocaleToAssocArray($mail->getContentHtml()),
                ]
            ];
            $this->mailTemplateRepository->create($mailTemplate, $this->context);
        }
    }

    public function remove(): void
    {
        $technicalNames = [];
        /** @var MailData $mail */
        foreach ($this->mails as $mail) {
            $technicalNames[] = $this->toSnakeCase($mail->getName());
        }

        // find all mail template type ids and mail template ids
        $mailTemplateTypeIds = $this->mailTemplateTypeRepository->searchIds(
            (new Criteria())
                ->addFilter(new EqualsAnyFilter('technicalName', $technicalNames)),
            $this->context
        )->getIds();

        if (empty($mailTemplateTypeIds)) {
            return;
        }

        $mailTemplateIds = $this->mailTemplateRepository->searchIds(
            (new Criteria())
                ->addFilter(new EqualsAnyFilter('mailTemplateTypeId', $mailTemplateTypeIds)),
            $this->context
        )->getIds();

        // prepare the id arrays for deletion
        $mailTemplateTypeIds = array_map(
            static fn(string $mailTemplateId) => ['id' => $mailTemplateId],
            $mailTemplateTypeIds
        );

        $mailTemplateIds = array_map(
            static fn(string $mailTemplateId) => ['id' => $mailTemplateId],
            $mailTemplateIds
        );

        // delete the mail templates and mail template types
        $this->mailTemplateRepository->delete($mailTemplateIds, $this->context);
        $this->mailTemplateTypeRepository->delete($mailTemplateTypeIds, $this->context);
    }
}
