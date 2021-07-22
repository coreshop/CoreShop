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

namespace CoreShop\Component\Resource\Helper;

class Tool
{
    /**
     * @static
     *
     * @param string $class
     *
     * @return bool
     */
    public static function classExists($class)
    {
        return self::classInterfaceExists($class, 'class');
    }

    /**
     * @static
     *
     * @param string $class
     *
     * @return bool
     */
    public static function interfaceExists($class)
    {
        return self::classInterfaceExists($class, 'interface');
    }

    /**
     * @param string $class
     * @param string $type
     *
     * @return bool
     */
    protected static function classInterfaceExists($class, $type)
    {
        $functionName = $type . '_exists';

        // if the class is already loaded we can skip right here
        if ($functionName($class, false)) {
            return true;
        }

        $class = '\\' . ltrim($class, '\\');

        // we need to set a custom error handler here for the time being
        // unfortunately suppressNotFoundWarnings() doesn't work all the time, it has something to do with the calls in
        // Pimcore\Tool::ClassMapAutoloader(), but don't know what actual conditions causes this problem.
        // but to be save we log the errors into the debug.log, so if anything else happens we can see it there
        // the normal warning is e.g. Warning: include_once(Path/To/Class.php): failed to open stream: No such file or directory in ...
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            //Logger::debug(implode(" ", [$errno, $errstr, $errfile, $errline]));
        });

        $exists = $functionName($class);

        restore_error_handler();

        return $exists;
    }
}
