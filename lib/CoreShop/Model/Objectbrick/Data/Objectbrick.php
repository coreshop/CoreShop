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

namespace CoreShop\Model\Objectbrick\Data;

use Pimcore\Model\Object\Objectbrick\Data\AbstractData;

/**
 * Class Objectbrick
 * @package CoreShop\Model\Objectbrick\Data
 * @todo: Refactor: Move to Object Namespace like Fieldcollection?
 */
class Objectbrick extends AbstractData
{
    /**
     *  Zend_View.
     */
    protected $view;

    /**
     * get View.
     *
     * @param $language
     *
     * @return \Zend_View
     */
    public function getView($language = null)
    {
        if (!$language) {
            $language = \CoreShop::getTools()->getLocale();
        }

        if (!$this->view) {
            $this->view = new \Zend_View();
        }

        $this->view->language = (string) $language;
        $this->view->brick = $this;

        $class = get_class($this);
        $class = explode('\\', $class);
        $class = array_pop($class);
        $class = strtolower($class);

        $this->view->setScriptPath(
            [
                CORESHOP_TEMPLATE_BASE . '/scripts/' . $class,
                CORESHOP_TEMPLATE_PATH . '/scripts/' . $class,
                PIMCORE_WEBSITE_PATH.'/views/scripts/coreshop/' . $class,
            ]
        );

        return $this->view;
    }

    /**
     * Render Cart.
     *
     * @return string|bool
     */
    public function renderCart()
    {
        return false;
    }

    /**
     * Render Invoice.
     *
     * @return string|bool
     */
    public function renderInvoice()
    {
        return false;
    }
}
