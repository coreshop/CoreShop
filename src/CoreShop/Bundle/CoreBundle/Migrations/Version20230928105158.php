<?php

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20230928105158 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $classUpdater = new ClassUpdate(
            $this->container->getParameter('coreshop.model.order_item.pimcore_class_name'),
        );

        if ($classUpdater->hasField('immutable')) {
            return;
        }

        $immutableField = [
            'name' => 'immutable',
            'title' => 'coreshop.order_item.immutable',
            'tooltip' => '',
            'mandatory' => false,
            'noteditable' => true,
            'index' => false,
            'locked' => false,
            'style' => '',
            'permissions' => null,
            'datatype' => 'data',
            'fieldtype' => 'booleanSelect',
            'relationType' => false,
            'invisible' => false,
            'visibleGridView' => false,
            'visibleSearch' => false,
            'yesLabel' => '',
            'noLabel' => '',
            'emptyLabel' => '',
            'options' => [
                [
                    'key' => '',
                    'value' => 0,
                ],
                [
                    'key' => '',
                    'value' => 1,
                ],
                [
                    'key' => '',
                    'value' => -1,
                ],
            ],
            'width' => '',
        ];

        $classUpdater->insertFieldAfter('order', $immutableField);
        $classUpdater->save();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
