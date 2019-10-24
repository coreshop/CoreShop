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

namespace CoreShop\Bundle\ProductQuantityPriceRulesBundle\Event;

use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ProductQuantityPriceRuleValidationEvent extends GenericEvent
{
    /**
     * @var Concrete
     */
    private $object;

    /**
     * @var array
     */
    private $data;

    /**
     * @param Concrete $object
     * @param array    $data
     */
    public function __construct(Concrete $object, array $data)
    {
        parent::__construct($object);

        $this->object = $object;
        $this->data = $data;
    }

    /**
     * @return Concrete
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
