<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180615093141 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $coreShopProductFields = [
            [
                'fieldName' => 'active',
                'yesValue' => 'active',
                'noValue' => 'inactive',
            ],
            [
                'fieldName' => 'digitalProduct',
                'yesValue' => 'isDigitalProduct',
                'noValue' => 'noDigitalProduct',
            ],
            [
                'fieldName' => 'isTracked',
                'yesValue' => 'isTracked',
                'noValue' => 'notTracked',
            ],
        ];

        $coreShopProductClass = $this->container->getParameter('coreshop.model.product.pimcore_class_name');

        $classUpdater = new ClassUpdate($coreShopProductClass);

        foreach ($coreShopProductFields as $updateField) {
            if ($classUpdater->hasField($updateField['fieldName'])) {
                $classUpdater->replaceFieldProperties(
                    $updateField['fieldName'],
                    [
                        'fieldtype' => 'booleanSelect',
                        'yesLabel' => $updateField['yesValue'],
                        'noLabel' => $updateField['noValue'],
                        'options' => [
                            0 => [
                                'key' => '',
                                'value' => 0,
                            ],
                            1 => [
                                'key' => $updateField['yesValue'],
                                'value' => 1,
                            ],
                            2 => [
                                'key' => $updateField['noValue'],
                                'value' => -1,
                            ],
                        ],
                    ]
                );
            }
        }
        $classUpdater->save();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $coreShopProductFields = [
            [
                'fieldName' => 'active',
                'yesValue' => 'active',
                'noValue' => 'inactive',
            ],
            [
                'fieldName' => 'digitalProduct',
                'yesValue' => 'isDigitalProduct',
                'noValue' => 'noDigitalProduct',
            ],
            [
                'fieldName' => 'isTracked',
                'yesValue' => 'isTracked',
                'noValue' => 'notTracked',
            ],
        ];

        $coreShopProductClass = $this->container->getParameter('coreshop.model.product.pimcore_class_name');

        $classUpdater = new ClassUpdate($coreShopProductClass);

        foreach ($coreShopProductFields as $updateField) {
            if ($classUpdater->hasField($updateField['fieldName'])) {
                $classUpdater->replaceFieldProperties($updateField['fieldName'], ['fieldtype' => 'checkbox']);
            }
        }
        $classUpdater->save();
    }
}
