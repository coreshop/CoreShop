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

namespace CoreShop\Bundle\CoreBundle\Test\Service;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

final class NotificationRuleListener implements NotificationRuleListenerInterface
{
    private string $cacheDir;
    private array $firedEvents = [];

    public function __construct(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    public function hasBeenFired($type)
    {
        $finder = new Finder();
        $finder->files()->name(sprintf('*.%s.notification', $type))->in($this->cacheDir);

        return $finder->count() > 0;
    }

    public function clear()
    {
        if (!is_dir($this->cacheDir)) {
            return;
        }

        $fs = new Filesystem();

        $finder = new Finder();
        $finder->files()->name('*.notification')->in($this->cacheDir);

        foreach ($finder as $file) {
            $fs->remove($file->getPath());
        }
    }

    public function applyNewFired(GenericEvent $type)
    {
        $data = [
            'subject' => $type->getSubject(),
            'arguments' => $type->getArguments()
        ];

        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }

        file_put_contents(sprintf('%s/%s.%s.notification', $this->cacheDir, uniqid(), $type->getSubject()), serialize($data));
    }
}
