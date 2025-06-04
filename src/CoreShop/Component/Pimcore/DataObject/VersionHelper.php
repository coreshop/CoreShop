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

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Version;

class VersionHelper
{
    /**
     * This function enables usage of versioning in Pimcore and resets the state of versioning automatically
     * after your functions is finished.
     *
     *
     * @return mixed
     */
    public static function useVersioning(\Closure $function, bool $enabled = true)
    {
        $backup = Version::$disabled;

        if ($enabled) {
            Version::enable();
        } else {
            Version::disable();
        }

        $result = $function();

        if ($backup) {
            Version::disable();
        } else {
            Version::enable();
        }

        return $result;
    }

    public static function getLatestVersion(Concrete $object): Concrete
    {
        $latestVersion = $object->getLatestVersion();
        if ($latestVersion) {
            /** @psalm-suppress InternalMethod */
            $latestObj = $latestVersion->loadData();
            if ($latestObj instanceof Concrete) {
                $object = $latestObj;
            }
        }

        return $object;
    }
}
