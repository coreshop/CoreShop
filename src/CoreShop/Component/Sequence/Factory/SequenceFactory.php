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

namespace CoreShop\Component\Sequence\Factory;

use CoreShop\Component\Resource\Exception\UnsupportedMethodException;
use CoreShop\Component\Sequence\Model\SequenceInterface;

class SequenceFactory implements SequenceFactoryInterface
{
    /**
     * @psalm-param class-string $className
     */
    public function __construct(
        private string $className,
    ) {
    }

    public function createNew()
    {
        throw new UnsupportedMethodException('createNew');
    }

    public function createWithType(string $type): SequenceInterface
    {
        $sequence = new $this->className();
        $sequence->setType($type);

        return $sequence;
    }
}
