<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\CurrencyBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CurrencyController extends ResourceController
{
    public function getConfigAction(Request $request): Response
    {
        $settings = [
            'decimal_precision' => $this->getParameter('coreshop.currency.decimal_precision'),
            'decimal_factor' => $this->getParameter('coreshop.currency.decimal_factor'),
        ];

        return $this->viewHandler->handle($settings);
    }
}
