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

declare(strict_types=1);

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\FrontendBundle\TemplateConfigurator\TemplateConfiguratorInterface;
use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\OrderSaleStates;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class QuoteController extends FrontendController
{
    public function showAction(
        Request $request,
        OrderRepositoryInterface $orderRepository,
        ShopperContextInterface $shopperContext,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        $quote = $orderRepository->find($request->get('quote'));

        if (!$shopperContext->hasCustomer()) {
            return $this->redirectToRoute('coreshop_index');
        }

        $currentCustomer = $shopperContext->getCustomer();

        if (!$quote instanceof OrderInterface || !$quote->getSaleState() !== OrderSaleStates::STATE_QUOTE) {
            return $this->redirectToRoute('coreshop_index');
        }

        if (!$quote->getCustomer() instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        if ($quote->getCustomer()->getId() !== $currentCustomer->getId()) {
            return $this->redirectToRoute('coreshop_index');
        }

        return $this->renderTemplate($templateConfigurator->findTemplate('Quote/show.html'), [
            'quote' => $quote,
        ]);
    }
}
