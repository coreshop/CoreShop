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

namespace CoreShop\Bundle\UserBundle\Event;

use CoreShop\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\Event;

final class RequestPasswordChangeEvent extends Event
{
    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var string
     */
    private $resetLink;

    /**
     * @param UserInterface $user
     * @param string            $resetLink
     */
    public function __construct(UserInterface $user, $resetLink)
    {
        $this->user = $user;
        $this->resetLink = $resetLink;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getResetLink()
    {
        return $this->resetLink;
    }
}
