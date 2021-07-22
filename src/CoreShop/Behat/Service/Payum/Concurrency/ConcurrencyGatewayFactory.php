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

declare(strict_types=1);

namespace CoreShop\Behat\Service\Payum\Concurrency;

use CoreShop\Behat\Service\Payum\Concurrency\Action\CaptureAction;
use CoreShop\Behat\Service\Payum\Concurrency\Action\ConvertPaymentAction;
use CoreShop\Behat\Service\Payum\Concurrency\Action\NotifyAction;
use CoreShop\Behat\Service\Payum\Concurrency\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class ConcurrencyGatewayFactory extends GatewayFactory
{
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => 'concurrency',
            'payum.factory_title' => 'Concurrency',
            'payum.action.capture' => new CaptureAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.notify' => new NotifyAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
        ]);
    }
}
