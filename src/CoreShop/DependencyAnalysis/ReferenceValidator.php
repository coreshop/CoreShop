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
        if ($from->getFirst() !== 'CoreShop' || $to->getFirst() !== 'CoreShop') {
            return true;
        }

        if (count($from->parts) < 2 || count($to->parts) < 2) {
            return true;
        }

        if ($from->parts[1] === 'Component' && $to->parts[1] === 'Bundle') {
            if ($to->parts[2] === 'PayumBundle') {
                return true;
            }

            if ($to->parts[2] === 'WorkflowBundle') {
                return true;
            }

            return false;
        }

        if ($from->parts[1] === 'Component' && $from->parts[2] !== 'Core' && $to->parts[2] === 'Core') {
            return false;
        }

        if ($from->parts[1] === 'Bundle' && $from->parts[2] !== 'CoreBundle' && $to->parts[2] === 'CoreBundle') {
            if ($from->parts[2] === 'FrontendBundle') {
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
            'Or a Component tries to access the Core Component'
        ];
    }
}
