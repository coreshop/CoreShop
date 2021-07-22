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

namespace CoreShop\Bundle\WorkflowBundle\History;

use Pimcore\Model\DataObject;

interface HistoryLoggerInterface
{
    /**
     * @param DataObject\Concrete $object
     * @param null                $message
     * @param null                $description
     * @param bool                $translate
     */
    public function log(DataObject\Concrete $object, $message = null, $description = null, $translate = false);
}
