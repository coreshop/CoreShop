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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Component\Core\Model\OrderInterface;
use Pimcore\Http\Request\Resolver\EditmodeResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MailController extends FrontendController
{
    public function mailAction(Request $request): Response
    {
        return $this->render($this->templateConfigurator->findTemplate('Mail/mail.html'));
    }

    public function orderConfirmationAction(Request $request): Response
    {
        $order = $request->attributes->get('object');
        $viewParameters = [];

        if (!$this->get(EditmodeResolver::class)->isEditmode($request) && $order instanceof OrderInterface) {
            $viewParameters['order'] = $order;
        }

        return $this->render($this->templateConfigurator->findTemplate('Mail/order-confirmation.html'), $viewParameters);
    }
}
