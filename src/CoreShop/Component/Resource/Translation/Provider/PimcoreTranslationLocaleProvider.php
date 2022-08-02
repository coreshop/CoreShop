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

namespace CoreShop\Component\Resource\Translation\Provider;

use Pimcore\Tool;

final class PimcoreTranslationLocaleProvider implements TranslationLocaleProviderInterface
{
    public function getDefinedLocalesCodes(): array
    {
        return Tool::getValidLanguages();
    }

    public function getDefaultLocaleCode(): string
    {
        return Tool::getDefaultLanguage();
    }
}
