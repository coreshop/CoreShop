<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Pimcore\Model\DataObject;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180115121745 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema)
    {
        $payment = [
            'fieldtype' => 'coreShopSerializedData',
            'phpdocType' => 'array',
            'allowedTypes' => [
                ],
            'maxItems' => 1,
            'name' => 'paymentSettings',
            'title' => 'Payment Settings',
            'tooltip' => '',
            'mandatory' => false,
            'noteditable' => true,
            'index' => false,
            'locked' => null,
            'style' => '',
            'permissions' => null,
            'datatype' => 'data',
            'columnType' => null,
            'queryColumnType' => null,
            'relationType' => false,
            'invisible' => false,
            'visibleGridView' => false,
            'visibleSearch' => false,
        ];

        $cart = $this->container->getParameter('coreshop.model.cart.pimcore_class_name');
        $classUpdater = new ClassUpdate($cart);
        if (!$classUpdater->hasField('paymentSettings')) {
            $classUpdater->insertFieldAfter('paymentProvider', $payment);
            $classUpdater->save();
        }

        $order = $this->container->getParameter('coreshop.model.order.pimcore_class_name');
        $classUpdater = new ClassUpdate($order);
        if (!$classUpdater->hasField('paymentSettings')) {
            $classUpdater->insertFieldAfter('paymentProvider', $payment);
            $classUpdater->save();
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }

    /**
     * @param $jsonFile
     * @param $brickName
     *
     * @return mixed|DataObject\Objectbrick\Definition
     *
     * @throws \Exception
     */
    private function createBrick($jsonFile, $brickName)
    {
        try {
            $objectBrick = DataObject\Objectbrick\Definition::getByKey($brickName);
        } catch (\Exception $e) {
            $objectBrick = new DataObject\Objectbrick\Definition();
            $objectBrick->setKey($brickName);
        }

        $json = file_get_contents($jsonFile);

        try {
            DataObject\ClassDefinition\Service::importObjectBrickFromJson($objectBrick, $json, false);
            $objectBrick->save();
        } catch (\Exception $e) {
            //keep quite.
        }

        \Pimcore::collectGarbage();
        $objectBrick->save();

        return $objectBrick;
    }
}
