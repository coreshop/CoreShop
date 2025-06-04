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

namespace CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler;

use CoreShop\Component\Pimcore\DataObject\CompositeLinkGenerator;
use CoreShop\Component\Registry\PrioritizedCompositeServicePass;

final class LinkGeneratorPass extends PrioritizedCompositeServicePass
{
    public const LINK_GENERATOR_TAG = 'coreshop.link_generator';

    public function __construct(
        ) {
        parent::__construct(
            CompositeLinkGenerator::class,
            CompositeLinkGenerator::class,
            self::LINK_GENERATOR_TAG,
            'addContext',
        );
    }
}
