<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Controller\Action\Helper;

use CoreShop\Controller\Action\Payment;
use CoreShop\Helper\Zend\Action;
use Pimcore\Controller\Action\Helper\ViewRenderer;

/**
 * Class PaymentForward
 * @package CoreShop\Controller\Plugin
 */
class PaymentForward extends \Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var Action
     */
    public $helper;

    /**
     * Constructor
     *
     * Grab local copies of various MVC objects
     */
    public function __construct()
    {
        $this->helper = new Action();
    }
    /**
     * @param $action
     * @param $controller
     * @param $module
     * @param array $params
     * @return string
     */
    public function direct($action, $controller, $module, $params = []) {
        Payment::$isActionForward = true;

        $result = $this->helper->action($action, $controller, $module, $params);

        Payment::$isActionForward = false;

        return $result;
    }
}
