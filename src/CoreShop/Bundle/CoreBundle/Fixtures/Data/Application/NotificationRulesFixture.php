<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Fixtures\Data\Application;

use CoreShop\Bundle\NotificationBundle\Form\Type\NotificationRuleType;
use CoreShop\Component\Notification\Repository\NotificationRuleRepositoryInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Pimcore\Model\Document;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class NotificationRulesFixture extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private string $installerResources,
        private KernelInterface $kernel,
        private NotificationRuleRepositoryInterface $notificationRuleRepository,
        private FormFactoryInterface $formFactory,
    ) {
    }

    public static function getGroups(): array
    {
        return ['application'];
    }

    public function load(ObjectManager $manager): void
    {
        $installResourcesDirectory = $this->installerResources;
        $jsonFile = $this->kernel->locateResource(
            sprintf('%s/data/%s.json', $installResourcesDirectory, 'notification-rules'),
        );

        $totalExistingRules = count($this->notificationRuleRepository->findAll());

        if (file_exists($jsonFile)) {
            $json = file_get_contents($jsonFile);

            try {
                $json = json_decode($json, true);
                $totalImported = 0;

                foreach ($json as $rule) {
                    try {
                        $existingRules = $this->notificationRuleRepository->findBy(['name' => $rule['name']]);

                        if (count($existingRules) > 0) {
                            continue;
                        }

                        foreach ($rule['actions'] as &$action) {
                            foreach ($action['configuration']['mails'] as &$mail) {
                                $document = Document::getByPath('/' . $mail);

                                if ($document instanceof Document\Email) {
                                    $mail = $document->getId();
                                } else {
                                    $mail = null;
                                }
                            }
                        }

                        $form = $this->formFactory->createNamed('', NotificationRuleType::class);
                        $form->submit($rule);

                        $notificationRule = $form->getData();
                        $notificationRule->setSort($totalExistingRules + $totalImported + 1);

                        $manager->persist($notificationRule);

                        ++$totalImported;
                    } catch (\Exception $ex) {
                        throw $ex;
                        //If some goes wrong, we just ignore it
                    }
                }
            } catch (\Exception) {
                //If some goes wrong, we just ignore it
                return;
            }
        }

        $manager->flush();
    }
}
