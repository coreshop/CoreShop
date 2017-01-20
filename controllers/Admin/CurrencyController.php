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
 * Class CoreShop_Admin_CountryController
 */
class CoreShop_Admin_CurrencyController extends Admin\Data
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
    protected $model = \CoreShop\Model\Currency::class;

    /**
     * @param \CoreShop\Model\AbstractModel $model
     * @param $config
     * @return mixed
     */
    protected function prepareTreeNodeConfig(\CoreShop\Model\AbstractModel $model, $config)
    {
        if($model instanceof \CoreShop\Model\Currency) {
            $config['symbol'] = $model->getSymbol();
            $config['active'] = $model->getActive();
        }

        return $config;
    }

    public function getExchangeRateProvidersAction()
    {
        $providersList = [];

        foreach (\CoreShop\Model\Currency\ExchangeRates::$providerList as $name => $class) {
            $providersList[] = ['name' => $name];
        }

        $this->_helper->json(['success' => true, 'data' => $providersList]);
    }
}
