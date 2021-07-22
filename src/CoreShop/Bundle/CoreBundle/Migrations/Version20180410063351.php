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
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Pimcore\Tool;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180410063351 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $list = $this->container->get('coreshop.repository.cart')->getList();
        $list->setCondition('localeCode IS NULL');
        $list->load();

        /**
         * @var $cart CartInterface
         */
        foreach ($list->getObjects() as $cart) {
            $order = $cart->getOrder();
            $customer = $cart->getCustomer();

            if ($order instanceof OrderInterface) {
                $cart->setLocaleCode($order->getLocaleCode());
            } elseif ($customer instanceof CustomerInterface && null !== $customer->getLocaleCode()) {
                $cart->setLocaleCode($customer->getLocaleCode());
            } else {
                $cart->setLocaleCode(Tool::getDefaultLanguage());
            }

            $cart->save();
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
