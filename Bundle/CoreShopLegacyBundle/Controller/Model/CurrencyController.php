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
 * Class CountryController
 *
 * @Route("/currency")
 */
class CurrencyController extends Admin\DataController
{
    /**
     * @var string
     */
    protected $permission = 'coreshop_permission_currencies';

    /**
     * @var array
     */
    protected $publicActions = ['list', 'exchange-rate-providers'];

    /**
     * @var string
     */
    protected $model = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Currency::class;

    /**
     * @param \CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model
     * @param $config
     * @return mixed
     */
    protected function prepareTreeNodeConfig(\CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel $model, $config)
    {
        if($model instanceof \CoreShop\Bundle\CoreShopLegacyBundle\Model\Currency) {
            $config['symbol'] = $model->getSymbol();
            $config['active'] = $model->getActive();
        }

        return $config;
    }

    /**
     * @return \Pimcore\Bundle\PimcoreAdminBundle\HttpFoundation\JsonResponse
     *
     * @Route("/get-exchange-rate-providers")
     */
    public function getExchangeRateProvidersAction()
    {
        $providersList = [];

        foreach (\CoreShop\Bundle\CoreShopLegacyBundle\Model\Currency\ExchangeRates::$providerList as $name => $class) {
            $providersList[] = ['name' => $name];
        }

        return $this->json(['success' => true, 'data' => $providersList]);
    }
}
