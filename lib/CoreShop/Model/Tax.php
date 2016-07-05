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

namespace CoreShop\Model;

/**
 * Class Tax
 * @package CoreShop\Model
 */
class Tax extends AbstractModel
{
    /**
     * @var array
     */
    protected $localizedValues = array('name');

    /**
     * @var int
     */
    public $id;

    /**
     * @var float
     */
    public $rate;

    /**
     * @var bool
     */
    public $active;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param string $language language
     *
     * @return string
     */
    public function getName($language = null)
    {
        return $this->getLocalizedFields()->getLocalizedValue('name', $language);
    }

    /**
     * @param string $name
     * @param string $language language
     */
    public function setName($name, $language = null)
    {
        $this->getLocalizedFields()->setLocalizedValue('name', $name, $language);
    }

    /**
     * @return float
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param float $rate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
    }

    /**
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }
}
