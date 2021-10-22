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

declare(strict_types=1);

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Context\CustomerNotFoundException;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\OrderSaleStates;
use Symfony\Component\HttpFoundation\Request;

class QuoteController extends FrontendController
{
    public function showAction(Request $request)
    {
        $quote = $this->get('coreshop.repository.order')->find($request->get('quote'));

        try {
            $currentCustomer = $this->get(CustomerContextInterface::class)->getCustomer();
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

        return $this->render($this->templateConfigurator->findTemplate('Quote/show.html'), [
            'quote' => $quote,
        ]);
    }
}
