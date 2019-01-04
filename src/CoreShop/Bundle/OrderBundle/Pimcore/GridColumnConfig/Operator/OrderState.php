<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
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
     *
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

        switch ($result->def->name) {
            case 'orderState':
                $workflow = 'coreshop_order';

                break;

            case 'paymentState':
                $workflow = 'coreshop_order_payment';

                break;

            case 'shippingState':
                $workflow = 'coreshop_order_shipment';

                break;

            case 'invoiceState':
                $workflow = 'coreshop_order_invoice';

                break;

            default:
                $result->value = '--';

                return $result;
        }

        $state = $this->workflowManager->getStateInfo($workflow, $result->value, false);

        $rgb = $this->hex2rgb($state['color']);
        $opacity = $workflow === 'coreshop_order' ? 1 : 0.3;

        if ($this->highlightLabel === true) {
            $textColor = $workflow === 'coreshop_order' ? $this->getContrastColor($rgb[0], $rgb[1], $rgb[2]) : 'black';
            $backgroundColor = join(',', $rgb);
            $result->value = '<span class="rounded-color" style="background-color: rgba(' . $backgroundColor . ', ' . $opacity . '); color: ' . $textColor . ';">' . $state['label'] . '</span>';
        } else {
            $result->value = $state['label'];
        }

        return $result;
    }

    /**
     * @param string $hex
     *
     * @return array
     */
    private function hex2rgb($hex)
    {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = [$r, $g, $b];

        return $rgb;
    }

    /**
     * @param int $r
     * @param int $g
     * @param int $b
     *
     * @return string
     */
    private function getContrastColor($r, $g, $b)
    {
        $l1 = 0.2126 * pow($r / 255, 2.2) +
            0.7152 * pow($g / 255, 2.2) +
            0.0722 * pow($b / 255, 2.2);

        $l2 = 0.2126 * pow(0 / 255, 2.2) +
            0.7152 * pow(0 / 255, 2.2) +
            0.0722 * pow(0 / 255, 2.2);

        if ($l1 > $l2) {
            $contrastRatio = (int) (($l1 + 0.05) / ($l2 + 0.05));
        } else {
            $contrastRatio = (int) (($l2 + 0.05) / ($l1 + 0.05));
        }

        if ($contrastRatio > 7) {
            return 'black';
        } else {
            return 'white';
        }
    }
}
