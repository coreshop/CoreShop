<?php

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassInstallerInterface;
use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Pimcore\Model\DataObject\Fieldcollection\Definition;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20220817144912 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $fc = Definition::getByKey('CoreShopPriceRuleItem');
        
        if (null !== $fc) {
            return;
        }

        $installer = $this->container->get(ClassInstallerInterface::class);
        $kernel = $this->container->get('kernel');

        if (!$installer) {
            return;
        }

        if (!$kernel) {
            return;
        }

        $file = $kernel->locateResource('@CoreShopOrderBundle/Resources/install/pimcore/fieldcollections/CoreShopPriceRuleItem.json');

        $installer->createFieldCollection($file, 'CoreShopPriceRuleItem');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
