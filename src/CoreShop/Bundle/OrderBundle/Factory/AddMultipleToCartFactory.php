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

namespace CoreShop\Bundle\OrderBundle\Factory;

use CoreShop\Bundle\OrderBundle\DTO\AddMultipleToCartInterface;

class AddMultipleToCartFactory implements AddMultipleToCartFactoryInterface
{
    protected $addMultipleToCartClass;

    public function __construct(string $addMultipleToCartClass)
    {
        $this->addMultipleToCartClass = $addMultipleToCartClass;
    }

    /**
     * {@inheritdoc}
     */
    public function createWithMultipleAddToCarts(array $addToCarts): AddMultipleToCartInterface
    {
        $class = new $this->addMultipleToCartClass($addToCarts);

        if (!in_array(AddMultipleToCartInterface::class, class_implements($class), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s".', get_class($class), AddMultipleToCartInterface::class)
            );
        }

        return $class;
    }
}
