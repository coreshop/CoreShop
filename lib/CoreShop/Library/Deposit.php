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
 * @author Stefan Hagspiel <shagspiel@dachcom.ch>
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Library;

class Deposit
{

    /**
     * @var null
     */
    public $depositNamespace = null;

    /**
     * @var array
     */
    public $depositData = array();

    /**
     * set max elements per deposit
     * set 0 for no limit
     *
     * @var array
     */
    public $maxElements = 0;

    const LIMIT_REACHED = 'limit_reached';
    const ALREADY_ADDED = 'already_added';

    /**
     *
     * Set Deposit Namespace
     * @param null $namespace
     *
     * @return $this
     */
    public function setNamespace($namespace = null)
    {
        $session = $this->getSession();

        $this->depositNamespace = $namespace;

        if (!isset($session->deposits)) {
            $session->deposits = array();
        }

        if (!isset($session->deposits[ $this->depositNamespace ])) {
            $session->deposits[ $this->depositNamespace ] = array();
        }

        $this->depositData = $session->deposits[ $this->depositNamespace ];

        return $this;
    }

    public function setLimit($limit = 0)
    {
        $this->maxElements = $limit;

        return $this;
    }

    /**
     *
     * Add a Element to Deposit
     * @param      $id
     * @param bool $value
     *
     * @return bool
     */
    public function add($id, $value = true)
    {
        if ($this->allowedToAdd($id) === true) {
            $this->depositData[ (int) $id ] = $value;
            $this->save();

            return true;
        }

        return false;
    }


    /**
     * Remove a Element from Deposit
     * @param $id
     */
    public function remove($id)
    {
        if (isset($this->depositData[ $id ])) {
            unset($this->depositData[ $id ]);
        }

        $this->save();
    }

    /**
     * Get formatted deposit
     * @return array
     */
    public function toArray()
    {
        if (empty($this->depositData)) {
            return array();
        }

        $data = array();

        foreach ($this->depositData as $id => $val) {
            $data[]= $id;
        }
        return $data;
    }

    /**
     * Check if element is allowed to add
     */
    public function allowedToAdd($id)
    {
        if ($this->maxElements !== 0 && count($this->depositData) >= $this->maxElements) {
            return self::LIMIT_REACHED;
        } elseif (isset($this->depositData[ $id ])) {
            return self::ALREADY_ADDED;
        }

        return true;
    }

    protected function getSession()
    {
        return \CoreShop\Tool::getSession();
    }

    protected function save()
    {
        $session = $this->getSession();

        $session->deposits[ $this->depositNamespace ] = $this->depositData;
    }
}
