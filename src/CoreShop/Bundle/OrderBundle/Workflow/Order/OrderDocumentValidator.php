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

namespace CoreShop\Bundle\OrderBundle\Workflow\Order;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Processable\ProcessableInterface;
use CoreShop\Component\Order\Workflow\ProposalValidatorInterface;
use CoreShop\Component\Order\Workflow\WorkflowManagerInterface;
use Webmozart\Assert\Assert;

final class OrderDocumentValidator implements ProposalValidatorInterface
{
    /**
     * @var ProcessableInterface
     */
    private $processableHelper;

    /**
     * @param ProcessableInterface $processableHelper
     */
    public function __construct(ProcessableInterface $processableHelper)
    {
        $this->processableHelper = $processableHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function isValidForState(ProposalInterface $proposal, $currentState, $newState)
    {
        /**
         * @var $proposal OrderInterface
         */
        Assert::isInstanceOf($proposal, OrderInterface::class);

        if ($newState === WorkflowManagerInterface::ORDER_STATUS_COMPLETE) {
            if (!$this->processableHelper->isFullyProcessed($proposal)) {
                return false;
            }
        }

        return true;
    }
}
