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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Test\Service;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

final class NotificationRuleListener implements NotificationRuleListenerInterface
{
    public function __construct(private string $cacheDir)
    {
    }

    public function hasBeenFired(string $type): bool
    {
        $finder = new Finder();
        $finder->files()->name(sprintf('*.%s.notification', $type))->in($this->cacheDir);

        return $finder->count() > 0;
    }

    public function clear(): void
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

    public function applyNewFired(GenericEvent $type): void
    {
        $data = [
            'subject' => $type->getSubject(),
            'arguments' => $type->getArguments(),
        ];

        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }

        file_put_contents(sprintf('%s/%s.%s.notification', $this->cacheDir, uniqid(), $type->getSubject()), serialize($data));
    }
}
