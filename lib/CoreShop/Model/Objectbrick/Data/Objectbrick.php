<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Objectbrick\Data;

use Pimcore\Model\Object\Objectbrick\Data\AbstractData;

class Objectbrick extends AbstractData
{
    /**
     *  Zend_View
     */
    protected $view;

    /**
     * @param $language
     * @return \Zend_View
     */
    public function getView($language = null)
    {
        if (!$language) {
            $language = \Zend_Registry::get("Zend_Locale");
        }

        if (!$this->view) {
            $this->view = new \Zend_View();
        }

        $this->view->language = (string) $language;
        $this->view->brick = $this;

        $this->view->setScriptPath(
            array(
                CORESHOP_TEMPLATE_PATH . '/scripts/' . strtolower(array_pop(explode('\\', get_class($this))))
            )
        );

        return $this->view;
    }

    /**
     * @return string|boolean
     */
    public function renderCart()
    {
        return false;
    }

    /**
     * @return string|bool
     */
    public function renderInvoice()
    {
        return false;
    }
}
