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

namespace CoreShop\Behat\Page\Frontend\Account;

use CoreShop\Bundle\TestBundle\Page\Frontend\AbstractFrontendPage;

class RegisterPage extends AbstractFrontendPage implements RegisterPageInterface
{
    public function getRouteName(): string
    {
        return 'coreshop_register';
    }
}
