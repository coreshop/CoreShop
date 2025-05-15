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
