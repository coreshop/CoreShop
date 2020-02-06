<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use CoreShop\Component\User\Model\UserInterface;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Pimcore\Model\DataObject\Service;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20200206155318 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->writeMessage('Create User Class');
        $jsonFile = $this->container->get('kernel')->locateResource('@CoreShopUserBundle/Resources/install/pimcore/classes/CoreShopUser.json');
        $this->container->get('coreshop.class_installer')->createClass($jsonFile, 'CoreShopUser');

        $userField = [
            'fieldtype' => 'manyToOneRelation',
            'width' => '',
            'assetUploadPath' => '',
            'relationType' => true,
            'queryColumnType' => [
                'id' => 'int(11)',
                'type' => 'enum("document","asset","object")',
            ],
            'phpdocType' => '\\Pimcore\\Model\\Document\\Page | \\Pimcore\\Model\\Document\\Snippet | \\Pimcore\\Model\\Document | \\Pimcore\\Model\\Asset | \\Pimcore\\Model\\DataObject\\AbstractObject',
            'objectsAllowed' => true,
            'assetsAllowed' => false,
            'assetTypes' => [],
            'documentsAllowed' => false,
            'documentTypes' => [],
            'lazyLoading' => true,
            'classes' => [
                'CoreShopUser',
            ],
            'pathFormatterClass' => '',
            'name' => 'user',
            'title' => 'User',
            'tooltip' => '',
            'mandatory' => false,
            'noteditable' => false,
            'index' => false,
            'locked' => false,
            'style' => '',
            'permissions' => null,
            'datatype' => 'data',
            'invisible' => false,
            'visibleGridView' => false,
            'visibleSearch' => false,
        ];

        $this->writeMessage('Update Customer Class');
        $classUpdater = new ClassUpdate($this->container->getParameter('coreshop.model.customer.pimcore_class_name'));

        if ($classUpdater->hasField('user')) {
            $classUpdater->insertFieldAfter('localeCode', $userField);
        }

        $classUpdater->replaceFieldProperties('password', [
            'noteditable' => true,
        ]);
        $classUpdater->replaceFieldProperties('passwordResetHash', [
            'noteditable' => true,
        ]);
        $classUpdater->replaceFieldProperties('isGuest', [
            'noteditable' => true,
        ]);

        $classUpdater->save();

        $this->writeMessage('Create Users and Update Customers');
        $customerRepository = $this->container->get('coreshop.repository.customer');

        /**
         * @var CustomerInterface $customer
         */
        foreach ($customerRepository->findAll() as $customer) {
            if ($customer->getIsGuest()) {
                continue;
            }

            /**
             * @var UserInterface $user
             */
            $user = $this->container->get('coreshop.factory.user')->createNew();
            $user->setEmail($customer->getEmail());
            $user->setPassword($customer->getPassword());
            $user->setParent(Service::createFolderByPath($this->container->getParameter('coreshop.folder.user')));
            $user->setKey($customer->getEmail());
            $user->save();

            $customer->setUser($user);
            $customer->save();
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
