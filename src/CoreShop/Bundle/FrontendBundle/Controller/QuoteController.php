<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Component\Customer\Context\CustomerNotFoundException;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\QuoteInterface;
use Symfony\Component\HttpFoundation\Request;

class QuoteController extends FrontendController
{
    public function showAction(Request $request)
    {
        $quote = $this->get('coreshop.repository.quote')->find($request->get('quote'));

        try {
            $currentCustomer = $this->get('coreshop.context.customer')->getCustomer();
        } catch (CustomerNotFoundException $ex) {
            return $this->redirectToRoute('coreshop_index');
        }

        if (!$quote instanceof QuoteInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        if (!$quote->getCustomer() instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        if ($quote->getCustomer()->getId() !== $currentCustomer->getId()) {
            return $this->redirectToRoute('coreshop_index');
        }

        return $this->renderTemplate($this->templateConfigurator->findTemplate('Quote/show.html'), [
            'quote' => $quote,
        ]);
    }
}
