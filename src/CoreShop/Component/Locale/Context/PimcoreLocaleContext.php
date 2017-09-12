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

namespace CoreShop\Component\Locale\Context;

use Pimcore\Localization\Locale;

class PimcoreLocaleContext implements LocaleContextInterface
{
    /**
     * @var Locale
     */
    private $pimcoreLocaleService;

    /**
     * @param Locale $pimcoreLocaleService
     */
    public function __construct(Locale $pimcoreLocaleService)
    {
        $this->pimcoreLocaleService = $pimcoreLocaleService;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleCode()
    {
        $pimcoreLocale = $this->pimcoreLocaleService->getLocale();

        if (null === $pimcoreLocale) {
            throw new LocaleNotFoundException();
        }

        return $pimcoreLocale;
    }
}
