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

namespace CoreShop\Bundle\ThemeBundle\Service;

use Pimcore\Http\Request\Resolver\DocumentResolver;

final class PimcoreDocumentPropertyResolver implements ThemeResolverInterface
{
    /**
     * @var ActiveThemeInterface
     */
    private $activeTheme;

    /**
     * @var DocumentResolver
     */
    private $documentResolver;

    /**
     * @param ActiveThemeInterface $activeTheme
     * @param DocumentResolver     $documentResolver
     */
    public function __construct(
        ActiveThemeInterface $activeTheme,
        DocumentResolver $documentResolver
    ) {
        $this->activeTheme = $activeTheme;
        $this->documentResolver = $documentResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveTheme(/*ActiveThemeInterface $activeTheme*/)
    {
        if (\func_num_args() === 0) {
            trigger_error(
                'Calling CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface::resolveTheme without the CoreShop\Bundle\ThemeBundle\Service\ActiveThemeInterface Service is deprecated since 2.1 and will be removed in 3.0.',
            E_USER_DEPRECATED
                );
            $activeTheme = $this->activeTheme;
        } else {
            $activeTheme = func_get_arg(0);
        }

        try {
            $document = $this->documentResolver->getDocument();

            if ($document && $document->getProperty('theme')) {
                $theme = $document->getProperty('theme');

                $activeTheme->addTheme($theme);
                $activeTheme->setActiveTheme($theme);
            }
        } catch (\Exception $ex) {
            throw new ThemeNotResolvedException($ex);
        }

        throw new ThemeNotResolvedException();
    }
}
