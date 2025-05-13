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

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\DataObject\ClassDefinition\LinkGeneratorInterface;

abstract class AbstractSluggableLinkGenerator implements LinkGeneratorInterface
{
    protected function slugify($string): string
    {
        if ($string === null) {
            return '';
        }

        return strtolower(
            trim(
                preg_replace('~[^0-9a-z]+~i', '-', html_entity_decode(
                    preg_replace(
                        '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i',
                        '$1',
                        htmlentities($string, \ENT_QUOTES, 'UTF-8'),
                    ),
                    \ENT_QUOTES,
                    'UTF-8',
                )),
                '-',
            ),
        );
    }
}
