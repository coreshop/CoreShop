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

namespace CoreShop\Bundle\PayumPaymentBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Response;

class PaymentProviderController extends ResourceController
{
    public function getConfigAction(): Response
    {
        $factoryResults = [];

        foreach (array_keys($this->container->getParameter('coreshop.gateway_factories')) as $factory) {
            $factoryResults[] = [
                'type' => $factory,
                'name' => $factory,
            ];
        }

        return $this->viewHandler->handle(
            [
                'success' => true,
                'factories' => $factoryResults,
            ],
        );
    }
}
