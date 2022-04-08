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

namespace CoreShop\Bundle\CoreBundle\Application;

final class Version
{
    public const MAJOR_VERSION = '3';

    public const MINOR_VERSION = '0';

    public const RELEASE_VERSION = '0';

    public const EXTRA_VERSION = 'beta.3';

    public static function getVersion(): string
    {
        return sprintf(
            '%s.%s.%s-%s',
            self::MAJOR_VERSION,
            self::MINOR_VERSION,
            self::RELEASE_VERSION,
            self::EXTRA_VERSION
        );
    }
}
