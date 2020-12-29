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

namespace CoreShop\Bundle\PayumBundle\Storage;

use CoreShop\Component\Payment\Model\PaymentInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;
use Payum\Core\Bridge\Doctrine\Storage\DoctrineStorage;

class LockingStorage extends DoctrineStorage
{
    protected function doFind($id)
    {
        $objectManager = $this->objectManager;

        if (($objectManager instanceof EntityManager) &&
            in_array(PaymentInterface::class, class_implements($this->modelClass), true) &&
            $objectManager->getConnection()->isTransactionActive()
        ) {
            $objectManager->getConnection()->setAutoCommit(false);

            return $objectManager->find($this->modelClass, $id, LockMode::PESSIMISTIC_WRITE);
        }

        return parent::doFind($id);
    }
}
