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

namespace CoreShop\Bundle\CoreBundle\Event;

use CoreShop\Component\Product\Model\ProductUnitDefinitionPriceInterface;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ProductStoreValuesUnitDefinitionPriceUnmarshalEvent extends GenericEvent
{
    /**
     * @var ProductUnitDefinitionPriceInterface
     */
    private $original;

    /**
     * @var ProductUnitDefinitionPriceInterface
     */
    private $versioned;

    /**
     * @param Concrete                            $object
     * @param ProductUnitDefinitionPriceInterface $original
     * @param ProductUnitDefinitionPriceInterface $versioned
     */
    public function __construct(
        Concrete $object,
        ProductUnitDefinitionPriceInterface $original,
        ProductUnitDefinitionPriceInterface $versioned
    ) {
        parent::__construct($object);

        $this->original = $original;
        $this->versioned = $versioned;
    }

    /**
     * @return ProductUnitDefinitionPriceInterface
     */
    public function getOriginal(): ProductUnitDefinitionPriceInterface
    {
        return $this->original;
    }

    /**
     * @param ProductUnitDefinitionPriceInterface $original
     */
    public function setOriginal(ProductUnitDefinitionPriceInterface $original): void
    {
        $this->original = $original;
    }

    /**
     * @return ProductUnitDefinitionPriceInterface
     */
    public function getVersioned(): ProductUnitDefinitionPriceInterface
    {
        return $this->versioned;
    }
}
