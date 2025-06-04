<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Context\CustomerNotFoundException;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\OrderSaleStates;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Service\Attribute\SubscribedService;

class QuoteController extends FrontendController
{
    public function showAction(Request $request)
    {
        $this->denyAccessUnlessGranted('CORESHOP_QUOTE_DETAIL');

        $quote = $this->container->get('coreshop.repository.order')->find($this->getParameterFromRequest($request, 'quote'));

        try {
            $currentCustomer = $this->container->get(CustomerContextInterface::class)->getCustomer();
        } catch (CustomerNotFoundException) {
            return $this->redirectToRoute('coreshop_index');
        }

        if (!$quote instanceof OrderInterface || $quote->getSaleState() !== OrderSaleStates::STATE_QUOTE) {
            return $this->redirectToRoute('coreshop_index');
        }

        if (!$quote->getCustomer() instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        if ($quote->getCustomer()->getId() !== $currentCustomer->getId()) {
            return $this->redirectToRoute('coreshop_index');
        }

        return $this->render($this->getTemplateConfigurator()->findTemplate('Quote/show.html'), [
            'quote' => $quote,
        ]);
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            CustomerContextInterface::class => CustomerContextInterface::class,
            new SubscribedService('coreshop.repository.order', OrderRepositoryInterface::class),
        ]);
    }
}
