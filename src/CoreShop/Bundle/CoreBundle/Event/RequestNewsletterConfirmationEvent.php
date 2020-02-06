<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Event;

use CoreShop\Component\Core\Model\UserInterface;
use Symfony\Component\EventDispatcher\Event;

final class RequestNewsletterConfirmationEvent extends Event
{
    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var string
     */
    private $confirmLink;

    /**
     * @param UserInterface     $user
     * @param string            $confirmLink
     */
    public function __construct(UserInterface $user, $confirmLink)
    {
        $this->user = $user;
        $this->confirmLink = $confirmLink;
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
    public function getConfirmLink()
    {
        return $this->confirmLink;
    }
}
