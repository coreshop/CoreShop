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

namespace CoreShop\Model\Product\Filter\Condition;

use CoreShop\Model\Product\Filter;
use CoreShop\Model\Product\Listing;

/**
 * Class AbstractCondition
 * @package CoreShop\Model\Product\Filter\Condition
 */
abstract class AbstractCondition
{
    /**
     * @var string
     */
    public $field;

    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $type;

    /**
     * @var mixed
     */
    public $preSelect;

    /**
     *  Zend_View.
     */
    protected $view;

    /**
     * @param $language
     *
     * @return \Zend_View
     */
    public function getView($language = null)
    {
        if (!$language) {
            $language = \Zend_Registry::get('Zend_Locale');
        }

        if (!$this->view) {
            $this->view = new \Zend_View();
        }

        $this->view->language = (string) $language;
        $this->view->brick = $this;

        $class = get_class($this);
        $class = explode('\\', $class);
        $class = array_pop($class);

        $this->view->setScriptPath(
            array(
                CORESHOP_TEMPLATE_BASE.'/scripts/coreshop/product/filter',
                CORESHOP_TEMPLATE_BASE.'/scripts/coreshop/product/filter/'.strtolower($class),
                CORESHOP_TEMPLATE_PATH.'/scripts/coreshop/product/filter',
                CORESHOP_TEMPLATE_PATH.'/scripts/coreshop/product/filter/'.strtolower($class),
                PIMCORE_WEBSITE_PATH.'/views/scripts/coreshop/' . strtolower($class),
            )
        );

        return $this->view;
    }

    /**
     * add Condition to Productlist.
     *
     * @param Filter  $filter
     * @param Listing $list
     * @param $currentFilter
     * @param $params
     * @param bool $isPrecondition
     *
     * @return array $currentFilter
     */
    abstract public function addCondition(Filter $filter, Listing $list, $currentFilter, $params, $isPrecondition = false);

    /**
     * render HTML for filter.
     *
     * @param Filter  $filter
     * @param Listing $list
     * @param $currentFilter
     *
     * @return mixed
     */
    public function render(Filter $filter, Listing $list, $currentFilter)
    {
        $rawValues = $list->getGroupByValues($this->getField(), true);
        $script = $this->getViewScript($filter, $list, $currentFilter);

        return $this->getView()->partial($script, array(
            'label' => $this->getLabel(),
            'currentValue' => $currentFilter[$this->getField()],
            'values' => array_values($rawValues),
            'fieldname' => $this->getField(),
        ));
    }

    /**
     * @param Filter  $filter
     * @param Listing $list
     * @param $currentFilter
     *
     * @return string
     */
    protected function getViewScript(Filter $filter, Listing $list, $currentFilter)
    {
        $script = $this->getType().'.php';

        if ($this->getView()->getScriptPath($this->getField().'.php')) {
            $script = $this->getField().'.php';
        }

        return $script;
    }

    /**
     * @param array $values
     */
    public function setValues(array $values)
    {
        foreach ($values as $key => $value) {
            if ($key == 'type') {
                continue;
            }

            $setter = 'set'.ucfirst($key);

            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getPreSelect()
    {
        return $this->preSelect;
    }

    /**
     * @param mixed $preSelect
     */
    public function setPreSelect($preSelect)
    {
        $this->preSelect = $preSelect;
    }
}
