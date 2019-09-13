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

class Version20171209110814 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     *
     * @throws \CoreShop\Component\Pimcore\Exception\ClassDefinitionFieldNotFoundException
     */
    public function up(Schema $schema)
    {
        $additionalDataField = [
            'fieldtype' => 'objectbricks',
            'phpdocType' => '\\Pimcore\\Model\\DataObject\\Objectbrick',
            'allowedTypes' => [],
            'maxItems' => '',
            'name' => 'additionalData',
            'title' => 'Additional Data',
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

        $cartClass = $this->container->getParameter('coreshop.model.cart.pimcore_class_name');
        $classUpdater = new ClassUpdate($cartClass);
        if (!$classUpdater->hasField('additionalData')) {
            $classUpdater->insertFieldAfter('comment', $additionalDataField);
            $classUpdater->save();
        }

        $orderClass = $this->container->getParameter('coreshop.model.order.pimcore_class_name');
        $classUpdater = new ClassUpdate($orderClass);
        if (!$classUpdater->hasField('additionalData')) {
            $classUpdater->insertFieldAfter('comment', $additionalDataField);
            $classUpdater->save();
        }

        $quoteClass = $this->container->getParameter('coreshop.model.quote.pimcore_class_name');
        $classUpdater = new ClassUpdate($quoteClass);
        if (!$classUpdater->hasField('additionalData')) {
            $classUpdater->insertFieldAfter('comment', $additionalDataField);
            $classUpdater->save();
        }

        //update translations
        $this->container->get('coreshop.resource.installer.shared_translations')->installResources(new NullOutput(), 'coreshop');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
