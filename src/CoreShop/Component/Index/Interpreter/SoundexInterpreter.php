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

namespace CoreShop\Component\Index\Interpreter;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;

class SoundexInterpreter implements InterpreterInterface
{
    /**
     * {@inheritdoc}
     */
    public function interpret($value, IndexableInterface $indexable, IndexColumnInterface $config, $interpreterConfig = [])
    {
        if (is_null($value)) {
            return null;
        }

        if (is_array($value)) {
            sort($value);
            $string = implode(' ', $value);
        } else {
            $string = (string) $value;
        }

        $soundEx = soundex($string);

        return (int) ord(substr($soundEx, 0, 1)) . substr($soundEx, 1);
    }
}
