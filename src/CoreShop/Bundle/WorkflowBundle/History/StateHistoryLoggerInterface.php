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

declare(strict_types=1);

namespace CoreShop\Bundle\WorkflowBundle\History;

use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Workflow\Event\Event;

interface StateHistoryLoggerInterface
{
    /**
     * @param Concrete $object
     * @param Event    $event
     */
    public function log(Concrete $object, Event $event): void;
}
