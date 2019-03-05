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

namespace CoreShop\Bundle\MenuBundle\Voter;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;
use Pimcore\Bundle\AdminBundle\Security\User\TokenStorageUserResolver;
use Pimcore\Model\User;

class PimcoreVoter implements VoterInterface
{
    /**
     * @var TokenStorageUserResolver
     */
    private $tokenStorageUserResolver;

    /**
     * @param TokenStorageUserResolver $tokenStorageUserResolver
     */
    public function __construct(TokenStorageUserResolver $tokenStorageUserResolver)
    {
        $this->tokenStorageUserResolver = $tokenStorageUserResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function matchItem(ItemInterface $item)
    {
        if (!$item->getAttribute('permission')) {
            return true;
        }

        $user = $this->tokenStorageUserResolver->getUser();

        if ($user instanceof User) {
            return $user->isAllowed($item->getAttribute('permission'));
        }

        return false;
    }
}
