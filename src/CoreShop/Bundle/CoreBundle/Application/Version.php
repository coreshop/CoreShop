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

namespace CoreShop\Bundle\CoreBundle\Application;

final class Version
{
    const BUILD_VERSION = '1';

    const MAJOR_VERSION = '2';
    const MINOR_VERSION = '0';
    const RELEASE_VERSION = '0';
    const EXTRA_VERSION = 'alpha.2';

    /**
     * @return string
     */
    public static function getVersion()
    {
        $version = sprintf('%s.%s.%s', self::MAJOR_VERSION, self::MINOR_VERSION, self::RELEASE_VERSION);

        if (self::EXTRA_VERSION) {
            $version = sprintf('%s-%s', $version, self::EXTRA_VERSION);
        }

        return $version;
    }
}
