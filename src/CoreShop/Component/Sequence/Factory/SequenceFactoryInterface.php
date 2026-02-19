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

namespace CoreShop\Component\Sequence\Factory;

use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Sequence\Model\SequenceInterface;

interface SequenceFactoryInterface extends FactoryInterface
{
    public function createWithType(string $type): SequenceInterface;
}
