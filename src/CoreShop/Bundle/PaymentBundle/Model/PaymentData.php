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

namespace CoreShop\Bundle\PaymentBundle\Model;

use Pimcore\Model\DataObject;
use CoreShop\Component\Resource\ImplementedByPimcoreException;

class PaymentData extends DataObject\Objectbrick\Data\AbstractData implements PaymentDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPaymentProvider()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentProvider($name)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getSettings()
    {
        return self::getSettings();
    }

    /**
     * {@inheritdoc}
     */
    public function setSettings($settings)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}