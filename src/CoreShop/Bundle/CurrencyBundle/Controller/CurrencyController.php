<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CurrencyBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;

class CurrencyController extends ResourceController
{
    public function getConfigAction(Request $request)
    {
        $settings = [
            'decimal_precision' => $this->getParameter('coreshop.currency.decimal_precision'),
            'decimal_factor' => $this->getParameter('coreshop.currency.decimal_factor'),
        ];

        return $this->viewHandler->handle($settings);
    }
}
