<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Locale\Context;

use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Tool;

class PimcoreLocaleContext implements LocaleContextInterface
{
    /**
     * @var LocaleServiceInterface
     */
    private $pimcoreLocaleService;

    /**
     * @param LocaleServiceInterface $pimcoreLocaleService
     */
    public function __construct(LocaleServiceInterface $pimcoreLocaleService)
    {
        $this->pimcoreLocaleService = $pimcoreLocaleService;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleCode()
    {
        $pimcoreLocale = $this->pimcoreLocaleService->findLocale();

        if (null === $pimcoreLocale) {
            throw new LocaleNotFoundException();
        }

        if (!Tool::isValidLanguage($pimcoreLocale)) {
            return Tool::getDefaultLanguage();
        }

        return $pimcoreLocale;
    }
}
