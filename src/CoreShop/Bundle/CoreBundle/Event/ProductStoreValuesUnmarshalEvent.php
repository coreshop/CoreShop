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

use CoreShop\Component\Core\Model\ProductStoreValuesInterface;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ProductStoreValuesUnmarshalEvent extends GenericEvent
{
    /**
     * @var ProductStoreValuesInterface
     */
    private $original;

    /**
     * @var ProductStoreValuesInterface
     */
    private $versioned;

    /**
     * @param Concrete                    $object
     * @param ProductStoreValuesInterface $original
     * @param ProductStoreValuesInterface $versioned
     */
    public function __construct(
        Concrete $object,
        ProductStoreValuesInterface $original,
        ProductStoreValuesInterface $versioned
    ) {
        parent::__construct($object);

        $this->original = $original;
        $this->versioned = $versioned;
    }

    /**
     * @return ProductStoreValuesInterface
     */
    public function getOriginal(): ProductStoreValuesInterface
    {
        return $this->original;
    }

     /**
     * @param ProductStoreValuesInterface $original
     */
    public function setOriginal(ProductStoreValuesInterface $original): void
    {
        $this->original = $original;
    }

    /**
     * @return ProductStoreValuesInterface
     */
    public function getVersioned(): ProductStoreValuesInterface
    {
        return $this->versioned;
    }
}
