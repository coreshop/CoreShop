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
 * Class CoreShop_Admin_ShopController
 */
class CoreShop_Admin_ShopController extends Admin\Data
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_shops';

    /**
     * @var string
     */
    protected $model = \CoreShop\Model\Shop::class;

    /**
     * @param \CoreShop\Model\AbstractModel $model
     */
    protected function setDefaultValues(\CoreShop\Model\AbstractModel $model) {
        if($model instanceof \CoreShop\Model\Shop) {
            $model->setTemplate(\CoreShop\Model\Shop::getDefaultShop()->getTemplate());
        }
    }
    
    public function listSitesAction()
    {
        $list = new \Pimcore\Model\Site\Listing();
        $list->setOrder('ASC');
        $list->load();

        $sites = [];
        if (is_array($list->getSites())) {
            foreach ($list->getSites() as $site) {
                if($site instanceof Site) {
                    $sites[] = [
                        'id' => $site->getId(),
                        'rootId' => $site->getRootId(),
                        'name' => $site->getMainDomain()
                    ];
                }
            }
        }
        $this->_helper->json($sites);
    }
}
