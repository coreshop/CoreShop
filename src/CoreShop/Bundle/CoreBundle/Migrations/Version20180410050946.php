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

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Pimcore\Tool;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180410050946 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $localeField = [
            'fieldtype' => 'language',
            'onlySystemLanguages' => false,
            'options' => [],
            'width' => '',
            'defaultValue' => null,
            'optionsProviderClass' => null,
            'optionsProviderData' => null,
            'queryColumnType' => 'varchar(190)',
            'columnType' => 'varchar(190)',
            'phpdocType' => 'string',
            'name' => 'localeCode',
            'title' => 'Locale',
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
        ];

        $locales = Tool::getSupportedLocales();
        $options = [];

        foreach ($locales as $short => $translation) {
            $options[] = [
                'key' => $translation,
                'value' => $short,
            ];
        }

        $localeField['options'] = $options;

        $order = $this->container->getParameter('coreshop.model.order.pimcore_class_name');
        $classUpdater = new ClassUpdate($order);

        if (!$classUpdater->hasField('localeCode')) {
            $classUpdater->insertFieldAfter('orderLanguage', $localeField);
            $classUpdater->save();
        }

        $quote = $this->container->getParameter('coreshop.model.quote.pimcore_class_name');
        $classUpdater = new ClassUpdate($quote);

        if (!$classUpdater->hasField('localeCode')) {
            $classUpdater->insertFieldAfter('quoteLanguage', $localeField);
            $classUpdater->save();
        }

        $customer = $this->container->getParameter('coreshop.model.customer.pimcore_class_name');
        $classUpdater = new ClassUpdate($customer);

        if (!$classUpdater->hasField('localeCode')) {
            $classUpdater->insertFieldAfter('locale', $localeField);
            $classUpdater->save();
        }

        $migrations = [
            $this->container->get('coreshop.repository.customer')->getClassId() => [
                'from' => 'locale',
                'to' => 'localeCode',
            ],
            $this->container->get('coreshop.repository.order')->getClassId() => [
                'from' => 'orderLanguage',
                'to' => 'localeCode',
            ],
            $this->container->get('coreshop.repository.quote')->getClassId() => [
                'from' => 'quoteLanguage',
                'to' => 'localeCode',
            ],
        ];

        foreach ($migrations as $classId => $configuration) {
            $storeTable = sprintf('object_store_%s', $classId);
            $queryTable = sprintf('object_query_%s', $classId);

            $queryStore = sprintf('UPDATE %s SET %s=%s', $storeTable, $configuration['to'], $configuration['from']);
            $queryQuery = sprintf('UPDATE %s SET %s=%s', $queryTable, $configuration['to'], $configuration['from']);

            $this->addSql($queryStore);
            $this->addSql($queryQuery);
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
