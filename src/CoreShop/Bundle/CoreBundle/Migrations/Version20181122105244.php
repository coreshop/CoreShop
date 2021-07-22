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

use CoreShop\Bundle\MoneyBundle\CoreExtension\Money;
use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20181122105244 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $cart = $this->container->getParameter('coreshop.model.cart.pimcore_class_name');
        $classUpdater = new ClassUpdate($cart);

        if ($classUpdater->hasField('shippingTaxRate')) {
            $field = $classUpdater->getFieldDefinition('shippingTaxRate');

            if ($field instanceof Money) {
                $classUpdater->replaceField(
                    'shippingTaxRate',
                    [
                        'fieldtype' => 'numeric',
                        'width' => '',
                        'defaultValue' => null,
                        'queryColumnType' => 'double',
                        'columnType' => 'double',
                        'phpdocType' => 'float',
                        'integer' => false,
                        'unsigned' => false,
                        'minValue' => null,
                        'maxValue' => null,
                        'unique' => false,
                        'decimalSize' => null,
                        'decimalPrecision' => null,
                        'name' => 'shippingTaxRate',
                        'title' => 'Shipping Tax Rate',
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
                        'visibleSearch' => false,
                    ]
                );
                $classUpdater->save();
            }
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
