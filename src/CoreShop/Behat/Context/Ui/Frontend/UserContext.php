<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Behat\Context\Ui\Frontend;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Page\Frontend\HomePageInterface;

final class UserContext implements Context
{
    public function __construct(
        private HomePageInterface $homePage,
    ) {
    }

    /**
     * @When I log out
     */
    public function iLogOut(): void
    {
        $this->homePage->logOut();
    }
}
