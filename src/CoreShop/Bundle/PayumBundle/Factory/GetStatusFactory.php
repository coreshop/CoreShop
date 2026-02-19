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

namespace CoreShop\Bundle\PayumBundle\Factory;

use CoreShop\Bundle\PayumBundle\Request\GetStatus;
use Payum\Core\Request\GetStatusInterface;

final class GetStatusFactory implements GetStatusFactoryInterface
{
    public function createNewWithModel($model): GetStatusInterface
    {
        return new GetStatus($model);
    }
}
