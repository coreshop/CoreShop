<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Behat\Listener;

use Behat\Testwork\EventDispatcher\Event\BeforeSuiteTested;
use CoreShop\Test\Setup;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\RebootableInterface;

final class SetupCacheClearListener implements EventSubscriberInterface
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var bool
     */
    private $cacheCleared = false;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeSuiteTested::BEFORE => ['clearCache', -200],
        ];
    }

    public function clearCache()
    {
        if (!Setup::setupDone()) {
            return;
        }

        if ($this->cacheCleared) {
            return;
        }

        $fs = new Filesystem();
        $fs->remove($this->kernel->getCacheDir());

        if ($this->kernel instanceof RebootableInterface) {
            $this->kernel->reboot(null);
        }

        $this->cacheCleared = true;
    }
}
