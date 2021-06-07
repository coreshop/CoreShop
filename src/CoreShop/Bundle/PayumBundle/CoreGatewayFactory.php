<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\PayumBundle;

use Http\Adapter\Guzzle7\Client;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Bridge\Symfony\ContainerAwareCoreGatewayFactory;

class CoreGatewayFactory extends ContainerAwareCoreGatewayFactory
{
    public function createConfig(array $config = [])
    {
        return parent::createConfig([
            'httplug.client'=>function (ArrayObject $config) {
                return new Client();
            }
        ]);
    }
}
