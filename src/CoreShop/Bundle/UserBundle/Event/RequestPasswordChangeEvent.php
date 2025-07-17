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

namespace CoreShop\Bundle\UserBundle\Event;

use CoreShop\Component\User\Model\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class RequestPasswordChangeEvent extends Event
{
    public function __construct(
        private UserInterface $user,
        private string $resetLink,
    ) {
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getResetLink(): string
    {
        return $this->resetLink;
    }
}
