<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\MoneyBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

class MoneyToIntegerTransformer implements DataTransformerInterface
{
    /**
     * @var int
     */
    private $decimalFactor;

    /**
     * @param int $decimalFactor
     */
    public function __construct(int $decimalFactor)
    {
        $this->decimalFactor = $decimalFactor;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        return $value / $this->decimalFactor;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        return (int) round($value * $this->decimalFactor);
    }
}
