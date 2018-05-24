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

namespace CoreShop\Component\Index\Interpreter;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;

interface LocalizedInterpreterInterface extends InterpreterInterface
{
    /**
     * @param $language
     * @param $value
     * @param IndexableInterface   $object
     * @param IndexColumnInterface $config
     *
     * @return mixed
     */
    public function interpretForLanguage($language, $value, IndexableInterface $object, IndexColumnInterface $config);
}
