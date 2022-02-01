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

namespace CoreShop\Bundle\SEOBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterSimpleRegistryTypePass;

final class ExtractorRegistryServicePass extends RegisterSimpleRegistryTypePass
{
    public const EXTRACTOR_TAG = 'coreshop.seo.extractor';

    public function __construct()
    {
        parent::__construct(
            'coreshop.registry.seo.extractor',
            'coreshop.seo.extractors',
            self::EXTRACTOR_TAG
        );
    }

}
