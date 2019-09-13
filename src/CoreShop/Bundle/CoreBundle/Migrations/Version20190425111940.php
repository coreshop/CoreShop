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
use Pimcore\Cache;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20190425111940 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $productClass = $this->container->getParameter('coreshop.model.product.pimcore_class_name');
        $productRepo = $this->container->get('coreshop.repository.product');

        $classId = $productRepo->getClassId();

        $updater = new ClassUpdate($productClass);

        if (!$updater->hasField('wholesaleBuyingPrice')) {
            $updater->insertFieldAfter('wholesalePrice', [
                'fieldtype' => 'coreShopMoneyCurrency',
                 'width' => '',
                 'phpdocType' => 'CoreShop\\Component\\Currency\\Model\\Money',
                 'minValue' => null,
                 'maxValue' => null,
                 'name' => 'wholesaleBuyingPrice',
                 'title' => 'Wholesale Buying Price',
                 'tooltip' => '',
                 'mandatory' => false,
                 'noteditable' => false,
                 'index' => false,
                 'locked' => null,
                 'style' => '',
                 'permissions' => null,
                 'datatype' => 'data',
                 'relationType' => false,
                 'invisible' => false,
                 'visibleGridView' => false,
                 'visibleSearch' => false,
            ]);
            $updater->save();
        }

        //Assume Standard Store Currency as wholesale Currency
        $store = $this->container->get('coreshop.repository.store')->findStandard();

        $this->addSql(sprintf(
            'UPDATE object_query_%s SET `wholesaleBuyingPrice__value` = `wholesalePrice`, `wholesaleBuyingPrice__currency` = %s',
            $classId,
            $store->getCurrency()->getId()
        ));
        $this->addSql(sprintf(
            'UPDATE object_store_%s SET `wholesaleBuyingPrice__value` = `wholesalePrice`, `wholesaleBuyingPrice__currency` = %s',
            $classId,
            $store->getCurrency()->getId()
        ));

        Cache::clearAll();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
