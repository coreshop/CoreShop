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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Taxation\Model;

use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreFieldcollection;

abstract class TaxItem extends AbstractPimcoreFieldcollection implements TaxItemInterface
{
    /**
     * @return string
     */
    public function getId()
    {
        return $this->getObject()->getId() . '_tax_item_' . $this->getIndex();
    }
}
