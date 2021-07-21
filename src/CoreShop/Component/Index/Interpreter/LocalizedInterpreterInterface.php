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

declare(strict_types=1);

namespace CoreShop\Component\Index\Interpreter;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;

interface LocalizedInterpreterInterface extends InterpreterInterface
{
    /**
     * @param string               $language
     * @param mixed                $value
     * @param IndexableInterface   $indexable
     * @param IndexColumnInterface $config
     * @param array                $interpreterConfig
     *
     * @return mixed
     */
    public function interpretForLanguage(
        string $language,
        $value,
        IndexableInterface $indexable,
        IndexColumnInterface $config,
        array $interpreterConfig = []
    );
}
