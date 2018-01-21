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

namespace CoreShop\Bundle\OrderBundle\Pimcore\GridColumnConfig\Operator;

use CoreShop\Component\Order\Workflow\WorkflowStateManagerInterface;
use Pimcore\DataObject\GridColumnConfig\Operator\AbstractOperator;

class OrderState extends AbstractOperator
{
    /**
     * @var bool
     */
    private $highlightLabel = false;

    /**
     * @var WorkflowStateManagerInterface
     */
    private $workflowManager;

    /**
     * OrderState constructor.
     *
     * @param WorkflowStateManagerInterface $workflowManager
     * @param \stdClass                     $config
     * @param null                          $context
     */
    public function __construct(WorkflowStateManagerInterface $workflowManager, \stdClass $config, $context = null)
    {
        parent::__construct($config, $context);
        $this->workflowManager = $workflowManager;
        $this->highlightLabel = $config->highlightLabel;
    }

    /**
     * @param \Pimcore\Model\Element\ElementInterface $element
     * @return null|\stdClass|string
     */
    public function getLabeledValue($element)
    {
        $result = new \stdClass();
        $result->label = $this->label;
        $children = $this->getChilds();

        if (!$children) {
            return $result;
        }

        $c = $children[0];
        $result = $c->getLabeledValue($element);

        $workflow = null;

        //todo: get child attribute instead of silly string comparing!
        if (strpos($result->label, 'orderState)') !== false) {
            $workflow = 'coreshop_order';
        } elseif (strpos($result->label, 'paymentState)') !== false) {
            $workflow = 'coreshop_order_payment';
        } elseif (strpos($result->label, 'shippingState)') !== false) {
            $workflow = 'coreshop_order_shipment';
        } elseif (strpos($result->label, 'invoiceState)') !== false) {
            $workflow = 'coreshop_order_invoice';
        } else {
            $result->value = '--';
            return $result;
        }

        $state = $this->workflowManager->getStateInfo($workflow, $result->value, false);

        if ($this->highlightLabel === true) {
            $result->value = '<span class="rounded-color" style="background-color:' . $state['color'] . '; color: black">' . $state['label'] . '</span>';
        } else {
            $result->value = $state['label'];
        }

        return $result;
    }

}
