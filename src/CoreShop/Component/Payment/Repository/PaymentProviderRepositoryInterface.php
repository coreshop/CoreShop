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

namespace CoreShop\Component\Payment\Repository;

use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;

interface PaymentProviderRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $title
     * @param string $locale
     *
     * @return PaymentProviderInterface[]
     */
    public function findByTitle(string $title, string $locale): array;

    /**
     * @return PaymentProviderInterface[]
     */
    public function findActive(): array;
}
