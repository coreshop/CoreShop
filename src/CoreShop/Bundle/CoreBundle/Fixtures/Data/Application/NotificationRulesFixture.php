<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Fixtures\Data\Application;

use CoreShop\Bundle\FixtureBundle\Fixture\VersionedFixtureInterface;
use CoreShop\Bundle\NotificationBundle\Form\Type\NotificationRuleType;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Pimcore\Model\Document;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class NotificationRulesFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '2.0';
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $installResourcesDirectory = $this->container->getParameter('coreshop.installer.resources');
        /**
         * @var KernelInterface $kernel
         */
        $kernel = $this->container->get('kernel');
        $jsonFile = $kernel->locateResource(sprintf('%s/data/%s.json', $installResourcesDirectory, 'notification-rules'));

        $totalExistingRules = count($this->container->get('coreshop.repository.notification_rule')->findAll());

        if (file_exists($jsonFile)) {
            $json = file_get_contents($jsonFile);

            try {
                $json = json_decode($json, true);
                $totalImported = 0;

                foreach ($json as $rule) {
                    try {
                        $existingRules = $this->container->get('coreshop.repository.notification_rule')->findBy(['name' => $rule['name']]);

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

                        $form = $this->container->get('form.factory')->createNamed('', NotificationRuleType::class);
                        $form->submit($rule);

                        $notificationRule = $form->getData();
                        $notificationRule->setSort($totalExistingRules + $totalImported + 1);

                        $this->container->get('doctrine.orm.entity_manager')->persist($notificationRule);

                        $totalImported++;
                    } catch (\Exception $ex) {
                        //If some goes wrong, we just ignore it
                    }
                }
            } catch (\Exception $ex) {
                //If some goes wrong, we just ignore it
                return;
            }
        }

        $this->container->get('doctrine.orm.entity_manager')->flush();
    }
}
