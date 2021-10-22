<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\PimcoreSecurityServiceInterface;
use CoreShop\Behat\Service\SharedStorageInterface;
use Pimcore\Model\User;

final class BackendSecurityContext implements Context
{
    public function __construct(private SharedStorageInterface $sharedStorage, private PimcoreSecurityServiceInterface $securityService)
    {
    }

    /**
     * @Given I am a logged in admin
     */
    public function iAmLoggedInAdmin(): void
    {
        $user = new User();
        $user
            ->setName('behat-admin')
            ->setPassword('behat-admin')
            ->setAdmin(true)
            ->setCloseWarning(false)
            ->save();

        $this->securityService->logIn($user);

        $this->sharedStorage->set('user', $user);
    }
}
