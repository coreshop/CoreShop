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

declare(strict_types=1);

namespace CoreShop\Behat\Service;

use Behat\Mink\Driver\PantherDriver;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Session;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;

class CookieSetter implements CookieSetterInterface
{
    protected Session $minkSession;
    protected $minkParameters;

    public function __construct(Session $minkSession, $minkParameters)
    {
        if (!is_array($minkParameters) && !$minkParameters instanceof \ArrayAccess) {
            throw new \InvalidArgumentException(sprintf(
                '"$minkParameters" passed to "%s" has to be an array or implement "%s".',
                self::class,
                \ArrayAccess::class
            ));
        }

        $this->minkSession = $minkSession;
        $this->minkParameters = $minkParameters;
    }

    public function setCookie($name, $value)
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
            if ($driver instanceof PantherDriver && $driver->getClient() === null) {
                return true;
            }
        }
        catch (DriverException $e) {
            return true;
        }

        if (false !== strpos($session->getCurrentUrl(), $this->minkParameters['base_url'])) {
            return false;
        }

        return true;
    }
}
