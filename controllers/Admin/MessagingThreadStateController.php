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

use CoreShop\Model\Messaging\Thread\State;
use CoreShop\Controller\Action\Admin;

/**
 * Class CoreShop_Admin_MessagingThreadStateController
 */
class CoreShop_Admin_MessagingThreadStateController extends Admin\Data
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_messaging_thread_state';

    /**
     * @var string
     */
    protected $model = \CoreShop\Model\Messaging\Thread\State::class;

    /**
     * @param \CoreShop\Model\AbstractModel $model
     * @param $config
     * @return mixed
     */
    protected function prepareTreeNodeConfig(\CoreShop\Model\AbstractModel $model, $config)
    {
        if($model instanceof \CoreShop\Model\Messaging\Thread\State) {
            $config['color'] = $model->getColor();
            $config['count'] = $model->getThreadsList()->count();
        }

        return $config;
    }

    /**
     * @param \CoreShop\Model\AbstractModel $model
     */
    protected function setDefaultValues(\CoreShop\Model\AbstractModel $model) {
        if($model instanceof \CoreShop\Model\Messaging\Thread\State) {
            $model->setFinished(false);
        }
    }
}
