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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Concrete;
use Webmozart\Assert\Assert;

final class RecycleBinContext implements Context
{
    /**
     * @Then /^the recycled (product) does not exist anymore$/
     */
    public function theRecycledProductDoesNotExistAnymore(Concrete $concrete): void
    {
        Assert::null(DataObject::getById($concrete->getId(), ['force' => true]));
    }
}
