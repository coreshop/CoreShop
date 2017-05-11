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

namespace CoreShop\Bundle\CoreBundle\Product\Rule\Condition;

use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Model\StoreInterface;

class StoresConditionChecker implements ConditionCheckerInterface
{
    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @param StoreContextInterface $storeContext
     */
    public function __construct(StoreContextInterface $storeContext)
    {
        $this->storeContext = $storeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($subject, array $configuration)
    {
        $store = $this->storeContext->getStore();

        if (!$store instanceof StoreInterface) {
            return false;
        }

        return in_array($store->getId(), $configuration['stores']);
    }
}
