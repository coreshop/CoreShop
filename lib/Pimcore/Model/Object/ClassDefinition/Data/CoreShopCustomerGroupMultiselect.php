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

namespace Pimcore\Model\Object\ClassDefinition\Data;

use Pimcore\Model;

use CoreShop\Model\CustomerGroup;

class CoreShopCustomerGroupMultiselect extends Model\Object\ClassDefinition\Data\Multiselect {

    /**
     * Static type of this element
     *
     * @var string
     */
    public $fieldtype = "coreShopCustomerGroupMultiselect";

    /** Restrict selection to comma-separated list of countries.
     * @var null
     */
    public $restrictTo = null;


    public function __construct() {
        $this->buildOptions();
    }

    public function __wakeup() {
        $this->buildOptions();
    }

    protected function buildOptions() {
        $groups = CustomerGroup::getAll();

        $options = array();

        foreach ($groups as $group) {
            $options[] = array(
                "key" => $group->getName(),
                "value" => $group->getId()
            );
        }

        $this->setOptions($options);
    }

    /**
     * @param string $restrictTo
     */
    public function setRestrictTo($restrictTo)
    {
        $this->restrictTo = $restrictTo;
    }

    /**
     * @return string
     */
    public function getRestrictTo()
    {
        return $this->restrictTo;
    }

    /**
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }
}
