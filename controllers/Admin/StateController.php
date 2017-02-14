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


use CoreShop\Controller\Action\Admin;

/**
 * Class CoreShop_Admin_StateController
 */
class CoreShop_Admin_StateController extends Admin\Data
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_states';

    /**
     * @var string
     */
    protected $model = \CoreShop\Model\State::class;

    /**
     * @param \CoreShop\Model\AbstractModel $model
     * @param $config
     * @return mixed
     */
    protected function prepareTreeNodeConfig(\CoreShop\Model\AbstractModel $model, $config)
    {
        if($model instanceof \CoreShop\Model\State) {
            $config['country'] = $model->getCountry() instanceof \CoreShop\Model\Country ? $model->getCountry()->getName() : '';
        }

        return $config;
    }

    public function countryAction()
    {
        $list = \CoreShop\Model\State::getList();
        $list->setOrder('ASC');
        $list->setOrderKey('name');
        $list->setCondition('countryId=?', [$this->getParam('countryId')]);
        $list->load();

        $states = [];
        if (is_array($list->getData())) {
            foreach ($list->getData() as $state) {
                $states[] = $this->getTreeNodeConfig($state);
            }
        }
        $this->_helper->json($states);
    }
}
