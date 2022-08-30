<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Component\StorageList\Context;

class StorageListNotFoundException extends \RuntimeException
{
    public function __construct($message = null, \Exception $previousException = null)
    {
        parent::__construct(
            $message ?: 'CoreShop was not able to find out the requested Storage List.',
            0,
            $previousException
        );
    }
}
