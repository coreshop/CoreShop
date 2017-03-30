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


namespace CoreShop\Bundle\CoreShopLegacyBundle\Controller\Model;

use CoreShop\Bundle\CoreShopLegacyBundle\Controller\Admin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class StateController
 *
 * @Route("/state")
 */
class StateController extends Admin\DataController
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_states';

    /**
     * @var string
     */
    protected $model = \CoreShop\Bundle\CoreShopLegacyBundle\Model\State::class;

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model
     * @param $config
     * @return mixed
     */
    protected function prepareTreeNodeConfig(\CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model, $config)
    {
        if($model instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\State) {
            $config['country'] = $model->getCountry() instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Country ? $model->getCountry()->getName() : '';
        }

        return $config;
    }

    /**
     * @Route("/country")
     */
    public function countryAction(Request $request)
    {
        $list = \CoreShop\Bundle\CoreShopLegacyBundle\Model\State::getList();
        $list->setOrder('ASC');
        $list->setOrderKey('name');
        $list->setCondition('countryId=?', [$request->get('countryId')]);
        $list->load();

        $states = [];
        if (is_array($list->getData())) {
            foreach ($list->getData() as $state) {
                $states[] = $this->getTreeNodeConfig($state);
            }
        }
        return $this->json($states);
    }
}
