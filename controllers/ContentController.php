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
 * Class CoreShop_ContentController
 */
class CoreShop_ContentController extends Action
{
    public function indexAction()
    {
    }

    public function contactAction()
    {
        if ($this->getRequest()->isPost()) {
            $name = '';
            $mail = '';
            $subject = '';
            $message = '';
        }
    }
}
