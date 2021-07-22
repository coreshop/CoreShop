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

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20200427113101 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $quoteClass = $this->container->getParameter('coreshop.model.quote.pimcore_class_name');
        $updater = new ClassUpdate($quoteClass);

        if (!$updater->hasField('basePaymentTotal')) {
            $updater->insertFieldAfter('baseTotalNet', [
                'fieldtype' => 'numeric',
                'width' => '',
                'defaultValue' => null,
                'queryColumnType' => 'double',
                'columnType' => 'double',
                'phpdocType' => 'float',
                'integer' => true,
                'unsigned' => false,
                'minValue' => null,
                'maxValue' => null,
                'unique' => false,
                'decimalSize' => null,
                'decimalPrecision' => null,
                'name' => 'basePaymentTotal',
                'title' => 'Base Payment Total',
                'tooltip' => '',
                'mandatory' => false,
                'noteditable' => true,
                'index' => false,
                'locked' => false,
                'style' => '',
                'permissions' => null,
                'datatype' => 'data',
                'relationType' => false,
                'invisible' => false,
                'visibleGridView' => false,
                'visibleSearch' => false
            ]);
        }

        if (!$updater->hasField('paymentTotal')) {
            $updater->insertFieldAfter('totalNet', [
                'fieldtype' => 'numeric',
                'width' => '',
                'defaultValue' => null,
                'queryColumnType' => 'double',
                'columnType' => 'double',
                'phpdocType' => 'float',
                'integer' => true,
                'unsigned' => false,
                'minValue' => null,
                'maxValue' => null,
                'unique' => false,
                'decimalSize' => null,
                'decimalPrecision' => null,
                'name' => 'paymentTotal',
                'title' => 'Payment Total',
                'tooltip' => '',
                'mandatory' => false,
                'noteditable' => true,
                'index' => false,
                'locked' => false,
                'style' => '',
                'permissions' => null,
                'datatype' => 'data',
                'relationType' => false,
                'invisible' => false,
                'visibleGridView' => false,
                'visibleSearch' => false
            ]);
        }

        $updater->save();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
