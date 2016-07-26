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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Tracking;

use CoreShop\Model\Cart;
use CoreShop\Model\Order;
use CoreShop\Model\Product;

/**
 * Class Tracker
 * @package CoreShop\Tracking
 */
abstract class ClientTracker extends Tracker {
    /**
     * @return \Zend_View
     */
    protected function buildView()
    {
        $view = new \Zend_View();

        $class = get_class($this);
        $class = explode('\\', $class);
        $class = array_pop($class);
        $class = strtolower(preg_replace('/(?<=[a-z])([A-Z]+)/', "-$1", $class));
        $class = strtolower($class);

        $view->setScriptPath(
            array(
                CORESHOP_PATH . '/views/scripts/tracking/' . $class,
                CORESHOP_TEMPLATE_BASE . '/scripts/tracking/' . $class,
                CORESHOP_TEMPLATE_PATH . '/scripts/tracking/' . $class,
                PIMCORE_WEBSITE_PATH.'/views/scripts/coreshop/tracking/' . $class,
            )
        );

        return $view;
    }

    /**
     * @param array $config
     * @return string
     */
    public function track($config)
    {
        $view = $this->buildView();

        $data = $config['data'];
        $viewName = $config['viewName'];

        foreach($data as $key=>$value) {
            $view->$key = $value;
        }

        return $view->render($viewName . ".php");
    }
}
