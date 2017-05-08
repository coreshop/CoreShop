<?php

namespace CoreShop\Bundle\CoreBundle\Migrations\Data\ORM;

use CoreShop\Bundle\NotificationBundle\Form\Type\NotificationRuleType;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Okvpn\Bundle\MigrationBundle\Fixture\VersionedFixtureInterface;
use Pimcore\Model\Document;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NotificationRulesFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface//, DependentFixtureInterface
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
    /*function getDependencies()
    {
        return [
            //TODO: NotificationRules do have a dependency on mails
        ];
    }*/

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $installResourcesDirectory = $this->container->getParameter('coreshop.installer.resources');
        $jsonFile = $this->container->get('kernel')->locateResource(sprintf('%s/data/%s.json', $installResourcesDirectory, 'notification-rules'));

        if (file_exists($jsonFile)) {
            $json = file_get_contents($jsonFile);

            try {
                $json = json_decode($json, true);

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

                        $this->container->get('doctrine.orm.entity_manager')->persist($notificationRule);
                    } catch (\Exception $ex) {

                    }
                }
            } catch (\Exception $ex) {
                return;
            }
        }

        $this->container->get('doctrine.orm.entity_manager')->flush();
    }
}