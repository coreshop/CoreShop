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

use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180328093814 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    private $hadSalutationsColumn = false;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if ($schema->hasTable('coreshop_country')) {
            if (!$schema->getTable('coreshop_country')->hasColumn('salutations')) {
                $schema->getTable('coreshop_country')->addColumn(
                    'salutations',
                    'simple_array',
                    [
                        'notnull' => false,
                    ]
                );
            } else {
                $this->hadSalutationsColumn = true;
            }
        }
        if ($schema->hasTable('coreshop_shipping_rule_group')) {
            if (!$schema->getTable('coreshop_shipping_rule_group')->hasColumn('stopPropagation')) {
                $schema->getTable('coreshop_shipping_rule_group')->addColumn(
                    'stopPropagation',
                    'boolean'
                );
            }
        }

        $this->container->get('pimcore.cache.core.handler')->clearTag('doctrine_pimcore_cache');
    }

    public function postUp(Schema $schema)
    {
        if ($this->hadSalutationsColumn) {
            return;
        }

        //add default salutation prefixes
        /** @var RepositoryInterface $countryRepository */
        $countryRepository = $this->container->get('coreshop.repository.country');
        $manager = $this->container->get('doctrine.orm.entity_manager');

        $defaultSalutations = ['mrs', 'mr'];

        /** @var CountryInterface $country */
        foreach ($countryRepository->findAll() as $country) {
            //set salutation
            $country->setSalutations($defaultSalutations);

            //update address format
            $addressFormat = $country->getAddressFormat();
            if (strpos($addressFormat, '%Text(firstname);') !== false && strpos($addressFormat, '%Text(salutation);') === false) {
                $addressFormat = str_replace('%Text(firstname);', '%Text(salutation); %Text(firstname);', $addressFormat);
                $country->setAddressFormat($addressFormat);
            }

            $manager->persist($country);
        }

        $manager->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
