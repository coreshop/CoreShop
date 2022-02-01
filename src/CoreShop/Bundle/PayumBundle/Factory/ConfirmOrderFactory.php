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

namespace CoreShop\Bundle\PayumBundle\Factory;

use CoreShop\Bundle\PayumBundle\Request\ConfirmOrder;
use CoreShop\Bundle\PayumBundle\Request\ConfirmOrderInterface;

final class ConfirmOrderFactory implements ConfirmOrderFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createNewWithModel($model): ConfirmOrderInterface
    {
        return new ConfirmOrder($model);
    }
}
