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

namespace CoreShop\Bundle\ThemeBundle\Exception;

class CircularDependencyFoundException extends \Exception
{
    /**
     * @param string[]   $themes
     * @param \Exception $previous
     */
    public function __construct(array $themes, ?\Exception $previous = null)
    {
        $cycle = $this->getCycleFromArray($themes);

        $message = sprintf(
            'Circular dependency was found while resolving theme "%s", caused by cycle "%s".',
            $this->getFirstTheme($themes),
            $this->formatCycleToString($cycle)
        );

        parent::__construct($message, 0, $previous);
    }

    private function getCycleFromArray(array $themes)
    {
        while (reset($themes) !== end($themes) || 1 === count($themes)) {
            array_shift($themes);
        }

        if (0 === count($themes)) {
            throw new \InvalidArgumentException('There is no cycle within given themes.');
        }

        return $themes;
    }

    private function formatCycleToString(array $themes)
    {
        return implode(' -> ', $themes);
    }

    /**
     * @param string[] $themes
     */
    private function getFirstTheme(array $themes)
    {
        return reset($themes);
    }
}
