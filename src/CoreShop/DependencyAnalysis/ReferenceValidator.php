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

namespace CoreShop\DependencyAnalysis;

use PhpParser\Node\Name;

class ReferenceValidator implements \PhpDA\Reference\ValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValidBetween(Name $from, Name $to)
    {
        if ('CoreShop' !== $from->getFirst() || 'CoreShop' !== $to->getFirst()) {
            return true;
        }

        if (count($from->parts) < 2 || count($to->parts) < 2) {
            return true;
        }

        if ('Component' === $from->parts[1] && 'Bundle' === $to->parts[1]) {
            if ('PayumBundle' === $to->parts[2]) {
                return true;
            }

            if ('WorkflowBundle' === $to->parts[2]) {
                return true;
            }

            return false;
        }

        if ('Component' === $from->parts[1] && 'Core' !== $from->parts[2] && 'Core' === $to->parts[2]) {
            return false;
        }

        if ('Bundle' === $from->parts[1] && 'CoreBundle' !== $from->parts[2] && 'CoreBundle' === $to->parts[2]) {
            if ('FrontendBundle' === $from->parts[2]) {
                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessages()
    {
        return [
            'Dependency from Component to Bundle is not allowed',
            'Or a Bundle tries to access the CoreBundle',
            'Or a Component tries to access the Core Component',
        ];
    }
}
