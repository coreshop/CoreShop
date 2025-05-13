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
