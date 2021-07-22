<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\UserInterface;
use CoreShop\Component\Pimcore\BatchProcessing\BatchListing;
use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Pimcore\Model\DataObject\Service;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20200206155318 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->write('Create User Class');

        $jsonFile = $this->container->get('kernel')->locateResource('@CoreShopCoreBundle/Resources/install/pimcore/classes/CoreShopUserBundle/CoreShopUser.json');
        $this->container->get('coreshop.class_installer')->createClass($jsonFile, 'CoreShopUser');

        $userField = [
            'fieldtype' => 'coreShopRelation',
            'stack' => 'coreshop.user',
            'relationType' => true,
            'objectsAllowed' => true,
            'assetsAllowed' => false,
            'documentsAllowed' => false,
            'width' => null,
            'assetUploadPath' => null,
            'assetTypes' =>
                array(),
            'documentTypes' =>
                array(),
            'classes' =>
                array(),
            'pathFormatterClass' => '',
            'name' => 'user',
            'title' => 'coreshop.customer.user',
            'tooltip' => '',
            'mandatory' => false,
            'noteditable' => false,
            'index' => false,
            'locked' => null,
            'style' => '',
            'permissions' => null,
            'datatype' => 'data',
            'invisible' => false,
            'visibleGridView' => false,
            'visibleSearch' => false,
        ];

        $this->write('Update Customer Class');
        $classUpdater = new ClassUpdate($this->container->getParameter('coreshop.model.customer.pimcore_class_name'));

        if (!$classUpdater->hasField('user')) {
            $classUpdater->insertFieldAfter('localeCode', $userField);
        }

        if ($classUpdater->hasField('password')) {
            $classUpdater->replaceFieldProperties('password', [
                'noteditable' => true,
            ]);
        }

        if ($classUpdater->hasField('passwordResetHash')) {
            $classUpdater->replaceFieldProperties('passwordResetHash', [
                'noteditable' => true,
            ]);
        }

        if ($classUpdater->hasField('isGuest')) {
            $classUpdater->replaceFieldProperties('isGuest', [
                'noteditable' => true,
            ]);
        }

        $classUpdater->save();

        $this->write('Create Users and Update Customers');
        $customerRepository = $this->container->get('coreshop.repository.customer');

        $customerList = $customerRepository->getList();
        $batchList = new BatchListing($customerList, 100);

        /**
         * @var CustomerInterface $customer
         */
        foreach ($batchList as $customer) {
            if ($customer->getIsGuest()) {
                continue;
            }

            /**
             * @var UserInterface $user
             */
            $user = $this->container->get('coreshop.factory.user')->createNew();
            $user->setLoginIdentifier(
                $this->container->getParameter('coreshop.customer.security.login_identifier') === 'username' && method_exists($customer, 'getUsername') ?
                    $customer->getUsername() :
                    $customer->getEmail()
            );
            $user->setPassword($customer->getPassword());
            $user->setParent(Service::createFolderByPath(sprintf(
                '/%s/%s',
                $customer->getFullPath(),
                $this->container->getParameter('coreshop.folder.user')
            )));
            $user->setCustomer($customer);
            $user->setKey($customer->getEmail());
            $user->save();

            $customer->setUser($user);
            $customer->save();
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
