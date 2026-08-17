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

namespace CoreShop\Component\Order\NumberGenerator;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Sequence\Generator\SequenceGeneratorInterface;

class SequenceNumberGenerator implements NumberGeneratorInterface
{
    public function __construct(
        protected SequenceGeneratorInterface $sequenceNumberGenerator,
        protected string $type,
    ) {
    }

    public function generate(ResourceInterface $model): string
    {
        return (string) $this->sequenceNumberGenerator->getNextSequenceForType($this->type);
    }
}
