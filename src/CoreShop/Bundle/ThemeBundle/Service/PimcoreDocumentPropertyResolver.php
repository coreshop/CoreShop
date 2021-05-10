<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ThemeBundle\Service;

use Pimcore\Http\Request\Resolver\DocumentResolver;

final class PimcoreDocumentPropertyResolver implements ThemeResolverInterface
{
    private DocumentResolver $documentResolver;

    public function __construct(DocumentResolver $documentResolver)
    {
        $this->documentResolver = $documentResolver;
    }

    public function resolveTheme(): string
    {
        try {
            $document = $this->documentResolver->getDocument();

            if ($document && $document->getProperty('theme')) {
                return $document->getProperty('theme');
            }
        } catch (\Exception $ex) {
            throw new ThemeNotResolvedException($ex);
        }

        throw new ThemeNotResolvedException();
    }
}
