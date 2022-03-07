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

use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Order\Model\SaleInterface;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Pimcore\Model\DataObject\Listing;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20191122074934 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $cartRepo = $this->container->get('coreshop.repository.cart');
        $orderRepo = $this->container->get('coreshop.repository.order');
        $quoteRepo = $this->container->get('coreshop.repository.quote');

        foreach ([$orderRepo, $quoteRepo, $cartRepo] as $repo) {
            /**
             * @var Listing $list
             */
            $list = $repo->getList();
            $list->setUnpublished(true);
            $sales = $list->load();

            /**
             * @var $sale SaleInterface
             */
            foreach ($sales as $sale) {
                foreach ($sale->getItems() as $item) {
                    $changed = false;

                    foreach ($item->getAdjustments() as $adjustment) {
                        if (!$adjustment->getTypeIdentifier() === AdjustmentInterface::CART_PRICE_RULE) {
                            continue;
                        }

                        $changed = true;
                        $adjustment->setNeutral(true);
                    }

                    if ($changed) {
                        $item->save();
                    }
                }
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
