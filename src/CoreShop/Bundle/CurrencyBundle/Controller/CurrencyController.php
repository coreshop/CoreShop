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
