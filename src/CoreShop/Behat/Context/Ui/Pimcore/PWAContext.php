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

namespace CoreShop\Behat\Context\Ui\Pimcore;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Page\Pimcore\PWAPageInterface;
use Webmozart\Assert\Assert;

final class PWAContext implements Context
{
    public function __construct(
        private PWAPageInterface $pwaPage,
    ) {
    }

    /**
     * @When I open resource ":application", ":resource"
     */
    public function iOpenResource(string $application, string $resource): void
    {
        $this->pwaPage->openResource($application, $resource);
    }

    /**
     * @Given the panel with id ":id" should be open
     */
    public function thePanelWithIdShouldBeOpen(string $id): void
    {
        Assert::true($this->pwaPage->hasPimcoreTabWithId($id));
    }
}
