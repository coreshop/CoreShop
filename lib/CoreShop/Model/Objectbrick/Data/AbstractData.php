<?
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

use CoreShop\Exception;

class AbstractData extends \Pimcore\Model\Object\Objectbrick\Data\AbstractData
{
    /**
    *  Zend_View
    */
    protected $view;
    
    public function getView()
    {
        if(!$this->view)
        {
            $this->view = new \Zend_View();
            $this->view->brick = $this;
            
            $this->view->setScriptPath(
                array(
                    PIMCORE_PLUGINS_PATH . '/CoreShop/views/scripts',
                    PIMCORE_WEBSITE_PATH . '/views/scripts/',
                    PIMCORE_WEBSITE_PATH . '/views/layouts/',
                    PIMCORE_WEBSITE_PATH . '/views/scripts/coreshop/',
                    PIMCORE_WEBSITE_PATH . '/views/scripts/coreshop/cart',
                )
            );
        }
        
        return $this->view;
    }
    
    public function render()
    {
        throw new Exception("not implemented");
    }
}