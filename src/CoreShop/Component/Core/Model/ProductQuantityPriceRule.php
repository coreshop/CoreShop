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

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRule as BaseProductQuantityPriceRule;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @psalm-suppress MissingConstructor
 */
class ProductQuantityPriceRule extends BaseProductQuantityPriceRule implements ProductQuantityPriceRuleInterface
{
    /**
     * @var ArrayCollection|QuantityRangeInterface[]
     */
    protected $ranges;

    public function __construct()
    {
        parent::__construct();

        $this->ranges = new ArrayCollection();
    }
}
