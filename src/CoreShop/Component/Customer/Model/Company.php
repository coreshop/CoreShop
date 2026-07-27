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

namespace CoreShop\Component\Customer\Model;

use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class Company extends AbstractPimcoreModel implements CompanyInterface
{
    public function isEqualTo(UserInterface $user): bool
    {
        return $user instanceof self && $user->getId() === $this->getId();
    }
}
