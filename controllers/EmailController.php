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

use CoreShop\Controller\Action;

/**
 * Class CoreShop_EmailController
 */
class CoreShop_EmailController extends Action
{
    public function init()
    {
        parent::init();

        if ($this->view->editmode) {
            $this->disableLayout();
        }
    }

    public function emailAction()
    {
    }

    public function orderAcceptedAction()
    {
        $this->view->params = $this->getAllParams();
    }

    public function orderPaidAction()
    {
        $this->view->params = $this->getAllParams();
    }

    public function messageCustomerReplyAction()
    {
        $this->view->message = $this->getParam('messageObject');

        if ($this->view->message instanceof \CoreShop\Model\Messaging\Message) {
            $this->view->thread = $this->view->message->getThread();
        }
    }

    public function orderConfirmationAction()
    {
        $this->view->order = $this->getParam("order");
    }
}
