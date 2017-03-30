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
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ShopController
 *
 * @Route("/shop")
 */
class ShopController extends Admin\DataController
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_shops';

    /**
     * @var string
     */
    protected $model = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Shop::class;

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model
     */
    protected function setDefaultValues(\CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model) {
        if($model instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Shop) {
            $model->setTemplate(\CoreShop\Bundle\CoreShopLegacyBundle\Model\Shop::getDefaultShop()->getTemplate());
        }
    }

    /**
     * @Route("/list-sites")
     */
    public function listSitesAction()
    {
        $list = new \Pimcore\Model\Site\Listing();
        $list->setOrder('ASC');
        $list->load();

        $sites = [];
        if (is_array($list->getSites())) {
            foreach ($list->getSites() as $site) {
                if($site instanceof \Pimcore\Model\Site) {
                    $sites[] = [
                        'id' => $site->getId(),
                        'rootId' => $site->getRootId(),
                        'name' => $site->getMainDomain()
                    ];
                }
            }
        }
        return $this->json($sites);
    }
}
