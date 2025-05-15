<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ProductQuantityPriceRulesBundle\Event;

use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ProductQuantityPriceRuleValidationEvent extends GenericEvent
{
    private Concrete $object;

    private array $data;

    public function __construct(
        Concrete $object,
        array $data,
    ) {
        parent::__construct($object);

        $this->object = $object;
        $this->data = $data;
    }

    public function getObject(): Concrete
    {
        return $this->object;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
