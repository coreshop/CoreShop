<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20171207161600 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $className = $this->container->getParameter('coreshop.model.customer.pimcore_class_name');

        $newField = [
            'fieldtype' => 'href',
            'width' => '',
            'assetUploadPath' => '',
            'relationType' => true,
            'queryColumnType' => [
                'id' => 'int(11)',
                'type' => "enum('document','asset','object')",
            ],
            'phpdocType' => '\\Pimcore\\Model\\Document\\Page | \\Pimcore\\Model\\Document\\Snippet | \\Pimcore\\Model\\Document | \\Pimcore\\Model\\Asset | \\Pimcore\\Model\\DataObject\\AbstractObject',
            'objectsAllowed' => true,
            'assetsAllowed' => false,
            'assetTypes' => [],
            'documentsAllowed' => false,
            'documentTypes' => [],
            'lazyLoading' => true,
            'classes' => [
                [
                    'classes' => $this->container->getParameter('coreshop.model.address.pimcore_class_name'),
                ],
            ],
            'pathFormatterClass' => '',
            'name' => 'defaultAddress',
            'title' => 'Default Address',
            'tooltip' => '',
            'mandatory' => false,
            'noteditable' => false,
            'index' => false,
            'locked' => false,
            'style' => '',
            'permissions' => null,
            'datatype' => 'data',
            'columnType' => null,
            'invisible' => false,
            'visibleGridView' => false,
            'visibleSearch' => false,
        ];

        $classUpdater = new ClassUpdate($className);

        if (!$classUpdater->hasField('defaultAddress')) {
            $classUpdater->insertFieldAfter('passwordResetHash', $newField);

            $classUpdater->save();

            $customers = $this->container->get('coreshop.repository.customer')->findAll();

            /**
             * @var $customer CustomerInterface
             */
            foreach ($customers as $customer) {
                $addresses = $customer->getAddresses();

                if (count($addresses) > 0) {
                    $customer->setDefaultAddress($addresses[0]);
                    $customer->save();
                }
            }
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
