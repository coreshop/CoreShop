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

namespace CoreShop\Component\Order\Transformer;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface TransformerEventDispatcherInterface
{
    public function dispatchPreEvent(string $modelName, ResourceInterface $model, array $params = []);

    public function dispatchPostEvent(string $modelName, ResourceInterface $model, array $params = []);
}
