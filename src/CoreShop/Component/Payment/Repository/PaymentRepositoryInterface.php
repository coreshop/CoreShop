<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Payment\Repository;

use CoreShop\Component\Payment\Model\PayableInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;

interface PaymentRepositoryInterface extends RepositoryInterface
{
    /**
     * @param PayableInterface $payable
     *
     * @return PaymentInterface[]
     *
     * @deprecated use findForPayable instead
     */
    public function findForOrder(PayableInterface $payable);

    /**
     * @param PayableInterface $payable
     *
     * @return PaymentInterface[]
     */
    public function findForPayable(PayableInterface $payable);
}
