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

namespace CoreShop\Bundle\ThemeBundle\Service;

use Pimcore\Http\Request\Resolver\DocumentResolver;
use Pimcore\Model\Document;

final class PimcoreDocumentPropertyResolver implements ThemeResolverInterface
{
    public function resolveTheme(array $params): string
    {
        try {
            if (isset($params['document']) && $params['document'] instanceof Document && $params['document']->getProperty('theme')) {
                return $params['document']->getProperty('theme');
            }
        } catch (\Exception $ex) {
            throw new ThemeNotResolvedException($ex);
        }

        throw new ThemeNotResolvedException();
    }
}
