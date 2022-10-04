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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\MoneyBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

class MoneyToIntegerTransformer implements DataTransformerInterface
{
    public function __construct(
        private int $decimalFactor,
    ) {
    }

    public function transform($value): ?float
    {
        if (null === $value) {
            return null;
        }

        return $value / $this->decimalFactor;
    }

    public function reverseTransform($value): ?int
    {
        if (null === $value) {
            return null;
        }

        return (int) round($value * $this->decimalFactor);
    }
}
