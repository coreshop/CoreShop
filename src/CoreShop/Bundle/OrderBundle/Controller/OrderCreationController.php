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

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\OrderTransitions;
use CoreShop\Component\Pimcore\DataObject\InheritanceHelper;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Webmozart\Assert\Assert;

class OrderCreationController extends AbstractSaleCreationController
{
    /**
     * {@inheritdoc}
     */
    protected function getTransformer()
    {
        return $this->get('coreshop.order.transformer.cart_to_order');
    }

    /**
     * {@inheritdoc}
     */
    protected function afterSaleCreation(ProposalInterface $sale)
    {
        Assert::isInstanceOf($sale, OrderInterface::class);

        InheritanceHelper::useInheritedValues(function() use ($sale) {
            $this->get('coreshop.state_machine_applier')->apply($sale, OrderTransitions::IDENTIFIER, OrderTransitions::TRANSITION_CONFIRM);
        }, true);
        
        $routeParams = [
            '_locale' => $sale->getLocaleCode(),
            'token' => $sale->getToken(),
        ];

        if ($sale->getStore()->getSiteId() > 0) {
            $routeParams['site'] = $sale->getStore()->getSiteId();
        }

        return [
            'reviseLink' => $this->generateUrl(
                'coreshop_order_revise',
                $routeParams,
                UrlGenerator::ABSOLUTE_URL
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getPermission()
    {
        return 'coreshop_order_create';
    }
}
