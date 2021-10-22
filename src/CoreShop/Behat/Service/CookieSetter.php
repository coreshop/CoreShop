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

namespace CoreShop\Behat\Service;

use Behat\Mink\Driver\PantherDriver;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Session;

class CookieSetter implements CookieSetterInterface
{
    public function __construct(protected Session $minkSession, protected array | \ArrayAccess $minkParameters)
    {
    }

    public function setCookie(string $name, string $value): void
    {
        $this->prepareMinkSessionIfNeeded($this->minkSession);

        $this->minkSession->setCookie($name, $value);
    }

    protected function prepareMinkSessionIfNeeded(Session $session): void
    {
        if ($this->shouldMinkSessionBePrepared($session)) {
            $session->visit(rtrim($this->minkParameters['base_url'] . '/en/shop', '/') . '/');
        }
    }

    protected function shouldMinkSessionBePrepared(Session $session): bool
    {
        $driver = $session->getDriver();

        try {
            if ($driver instanceof PantherDriver && null === $driver->getClient()) {
                return true;
            }
        } catch (DriverException) {
            return true;
        }

        return !(str_contains($session->getCurrentUrl(), $this->minkParameters['base_url']));
    }
}
