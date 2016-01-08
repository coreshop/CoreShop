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

namespace CoreShop;

use CoreShop\Exception\UnsupportedException;
use Pimcore\Model\Document;
use Pimcore\Model\Object;

abstract class Theme {

    /**
     * @returns string
     * @throws UnsupportedException
     */
    public function getName() {
        throw new UnsupportedException("Please implement me");
    }

    /**
     * Get Theme Path
     *
     * @return string
     * @throws UnsupportedException
     */
    public function getTemplatePath() {
        return $templatePath = CORESHOP_TEMPLATE_BASE_PATH . "/" . $this->getName();
    }

    /**
     * Get Theme Config
     *
     * @return array
     * @throws UnsupportedException
     * @throws \Exception
     */
    public function getConfig() {
        $templatePath = $this->getTemplatePath();

        //Get Template Xml
        if(!file_exists("$templatePath/template.xml")) {
            throw new \Exception("Template " . $this->getName() . " not found");
        }

        $config = new \Zend_Config_Xml("$templatePath/template.xml");
        return $config->toArray();
    }

    /**
     * Installs a Theme
     *
     * @throws \Exception
     */
    public function installTheme()
    {
        $config = $this->getConfig();

        if(array_key_exists("installation", $config))
        {
            //Install CoreShop Documents

        }
    }

    /**
     * Installs Theme Demo Data if available
     *
     * @return bool
     */
    public function installDemoData() {
        return true;
    }
}