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

use CoreShop\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

class MailController extends FrontendController
{
    public function mailAction(Request $request)
    {
        return $this->renderTemplate('CoreShopFrontendBundle:Mail:mail.html.twig');
    }

    public function orderConfirmationAction(Request $request) {
        $order = $request->get('object');

        Assert::isInstanceOf($order, OrderInterface::class);

        return $this->renderTemplate('CoreShopFrontendBundle:Mail:order-confirmation.html.twig', [
            'order' => $order
        ]);
    }
}
