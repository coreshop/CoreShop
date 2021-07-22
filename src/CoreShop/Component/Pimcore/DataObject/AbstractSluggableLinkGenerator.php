<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\DataObject\ClassDefinition\LinkGeneratorInterface;

abstract class AbstractSluggableLinkGenerator implements LinkGeneratorInterface
{
    protected function slugify($string)
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
                        htmlentities($string, ENT_QUOTES, 'UTF-8')
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                )),
                '-'
            )
        );
    }
}
