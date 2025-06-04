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

namespace CoreShop\Component\Pimcore\Migration;

use Pimcore\Model\Translation;

class SharedTranslation
{
    public static function add(string $key, string $language, string $value)
    {
        $translationKey = Translation::getByKey($key, Translation::DOMAIN_DEFAULT, true);

        if ($translationKey) {
            $translationKey->addTranslation($language, $value);
            $translationKey->save();
        }
    }

    public static function cleanup(): void
    {
        $list = new Translation\Listing();
        $list->setDomain(Translation::DOMAIN_DEFAULT);

        if (method_exists($list, 'cleanup')) {
            $list->cleanup();
        }
    }
}
