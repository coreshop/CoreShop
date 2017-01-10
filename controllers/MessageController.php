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
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

use CoreShop\Controller\Action;

/**
 * Class CoreShop_MessageController
 */
class CoreShop_MessageController extends Action
{
    public function contactAction()
    {
        $this->view->contacts = \CoreShop\Model\Messaging\Contact::getList()->load();
        $this->view->params = $this->getAllParams();

        if ($this->view->params['token']) {
            $thread = \CoreShop\Model\Messaging\Thread::getByField('token', $this->view->params['token']);

            if ($thread instanceof \CoreShop\Model\Messaging\Thread) {
                $this->view->params['contactId'] = $thread->getContactId();
                $this->view->params['email'] = $thread->getEmail();

                if ($thread->getOrder() instanceof \CoreShop\Model\Order) {
                    $this->view->params['orderNumber'] = $thread->getOrder()->getOrderNumber();
                }
            }
        }

        if ($this->getRequest()->isPost()) {
            $result = \CoreShop\Model\Messaging\Service::handleRequestAndCreateThread($this->getAllParams(), $this->language);

            if ($result['success']) {
                $this->view->success = true;
            } else {
                $this->view->success = false;
                $this->view->error = $this->view->translate($result['message']);
            }
        }
    }
}
