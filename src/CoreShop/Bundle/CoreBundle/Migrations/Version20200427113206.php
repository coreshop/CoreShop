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

use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\Model\SaleInterface;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20200427113206 extends AbstractPimcoreMigration implements ContainerAwareInterface
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
            $list = $repo->getList();
            $list->setUnpublished(true);
            $sales = $list->load();

            /**
             * @var $sale SaleInterface
             */
            foreach ($sales as $sale) {

                $sale->setPaymentTotal(
                    (int)round(
                        (
                            round(
                                $sale->getTotal() / $this->container->getParameter('coreshop.currency.decimal_factor'),
                                $this->container->getParameter('coreshop.currency.decimal_precision')
                            ) * 100),
                0)
                );

                if ($sale instanceof OrderInterface) {
                    $sale->setBasePaymentTotal(
                        (int)round(
                            (
                                round(
                                    $sale->getBaseTotal() / $this->container->getParameter('coreshop.currency.decimal_factor'),
                                    $this->container->getParameter('coreshop.currency.decimal_precision')
                                ) * 100),
                    0)
                    );
                }

                $sale->save();
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
