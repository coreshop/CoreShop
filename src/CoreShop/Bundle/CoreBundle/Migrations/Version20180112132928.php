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

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180112132928 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema)
    {
        $tokenField = [
            'fieldtype' => 'input',
            'width' => null,
            'queryColumnType' => 'varchar',
            'columnType' => 'varchar',
            'columnLength' => 190,
            'phpdocType' => 'string',
            'regex' => '',
            'unique' => false,
            'name' => 'token',
            'title' => 'Token',
            'tooltip' => '',
            'mandatory' => false,
            'noteditable' => true,
            'index' => false,
            'locked' => null,
            'style' => '',
            'permissions' => null,
            'datatype' => 'data',
            'relationType' => false,
            'invisible' => true,
            'visibleGridView' => false,
            'visibleSearch' => false,
        ];

        $order = $this->container->getParameter('coreshop.model.order.pimcore_class_name');
        $classUpdater = new ClassUpdate($order);
        if (!$classUpdater->hasField('token')) {
            $classUpdater->insertFieldAfter('orderNumber', $tokenField);
            $classUpdater->save();
        }

        if ($classUpdater->hasField('paymentFeeNet')) {
            $classUpdater->removeField('paymentFeeNet');
        }
        if ($classUpdater->hasField('paymentFeeGross')) {
            $classUpdater->removeField('paymentFeeGross');
        }
        if ($classUpdater->hasField('paymentFeeTaxRate')) {
            $classUpdater->removeField('paymentFeeTaxRate');
        }
        if ($classUpdater->hasField('basePaymentFeeNet')) {
            $classUpdater->removeField('basePaymentFeeNet');
        }
        if ($classUpdater->hasField('basePaymentFeeGross')) {
            $classUpdater->removeField('basePaymentFeeGross');
        }

        $classUpdater->save();

        $cart = $this->container->getParameter('coreshop.model.cart.pimcore_class_name');
        $classUpdater = new ClassUpdate($cart);
        if ($classUpdater->hasField('paymentFeeGross')) {
            $classUpdater->removeField('paymentFeeGross');
        }
        if ($classUpdater->hasField('paymentFeeNet')) {
            $classUpdater->removeField('paymentFeeNet');
        }

        $classUpdater->save();

        $orderInvoice = $this->container->getParameter('coreshop.model.order_invoice.pimcore_class_name');
        $classUpdater = new ClassUpdate($orderInvoice);
        if ($classUpdater->hasField('basePaymentFeeGross')) {
            $classUpdater->removeField('basePaymentFeeGross');
        }
        if ($classUpdater->hasField('basePaymentFeeTax')) {
            $classUpdater->removeField('basePaymentFeeTax');
        }
        if ($classUpdater->hasField('basePaymentFeeNet')) {
            $classUpdater->removeField('basePaymentFeeNet');
        }

        $classUpdater->save();

        //update static routes (order controller added)
        $this->container->get('coreshop.resource.installer.routes')->installResources(new NullOutput(), 'coreshop');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
