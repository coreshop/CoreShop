<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\CoreBundle\Application;

final class Version
{
    public const string MAJOR_VERSION = '5';

    public const string MINOR_VERSION = '0';

    public const string RELEASE_VERSION = '0';

    public const string EXTRA_VERSION = 'pre.alpha';

    public static function getVersion(): string
    {
        $version = sprintf('%s.%s.%s', self::MAJOR_VERSION, self::MINOR_VERSION, self::RELEASE_VERSION);

        /** @psalm-suppress RedundantCondition */
        if (self::EXTRA_VERSION !== '') {
            $version = sprintf('%s-%s', $version, self::EXTRA_VERSION);
        }

        return $version;
    }
}
