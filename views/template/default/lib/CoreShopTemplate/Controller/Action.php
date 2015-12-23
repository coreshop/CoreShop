<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

namespace CoreShopTemplate\Controller;

use CoreShop\Controller\Action as CoreShopAction;

class Action extends CoreShopAction
{
    public function init()
    {
        parent::init();

        $this->view->headTitle()->setSeparator(" " . ("|") . " ");
    }
}
