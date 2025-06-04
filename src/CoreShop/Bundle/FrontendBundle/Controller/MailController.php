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
use Pimcore\Http\Request\Resolver\EditmodeResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\SubscribedService;

class MailController extends FrontendController
{
    public function mailAction(Request $request): Response
    {
        return $this->render($this->getTemplateConfigurator()->findTemplate('Mail/mail.html'));
    }

    public function orderConfirmationAction(Request $request): Response
    {
        $order = $request->attributes->get('object');
        $viewParameters = [];

        if (!$this->container->get(EditmodeResolver::class)->isEditmode($request) && $order instanceof OrderInterface) {
            $viewParameters['order'] = $order;
        }

        return $this->render($this->getTemplateConfigurator()->findTemplate('Mail/order-confirmation.html'), $viewParameters);
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                new SubscribedService(EditmodeResolver::class, EditmodeResolver::class),
            ],
        );
    }
}
