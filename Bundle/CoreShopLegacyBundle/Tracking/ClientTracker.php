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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Tracking;




use Pimcore\View;

/**
 * Class Tracker
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Tracking
 */
abstract class ClientTracker extends Tracker
{
    /**
     * @return View
     */
    protected function buildView()
    {
        $view = new View();

        $class = get_class($this);
        $class = explode('\\', $class);
        $class = array_pop($class);
        $class = strtolower(preg_replace('/(?<=[a-z])([A-Z]+)/', "-$1", $class));
        $class = strtolower($class);

        $view->setScriptPath(
            [
                CORESHOP_PATH . '/views/scripts/tracking/' . $class,
                CORESHOP_TEMPLATE_BASE . '/scripts/tracking/' . $class,
                CORESHOP_TEMPLATE_PATH . '/scripts/tracking/' . $class,
                PIMCORE_WEBSITE_PATH.'/views/scripts/coreshop/tracking/' . $class,
            ]
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

        foreach ($data as $key=>$value) {
            $view->$key = $value;
        }

        return $view->render($viewName . ".php");
    }
}
