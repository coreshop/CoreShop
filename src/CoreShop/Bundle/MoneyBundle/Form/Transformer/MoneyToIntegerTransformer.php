<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\MoneyBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

class MoneyToIntegerTransformer implements DataTransformerInterface
{
    public function __construct(private int $decimalFactor)
    {
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
