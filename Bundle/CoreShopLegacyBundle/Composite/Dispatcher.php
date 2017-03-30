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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Composite;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class Dispatcher
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Composite
 */
class Dispatcher
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $subclassOf;

    /**
     * @var array
     */
    public $types = [];

    /**
     * Dispatcher constructor.
     *
     * @param string $type
     * @param string $subclassOf
     */
    public function __construct($type, $subclassOf)
    {
        $this->type = $type;
        $this->subclassOf = $subclassOf;

        $event = new GenericEvent($this);

        // allow to register conditions here (e.g. through plugins)
        \Pimcore::getEventDispatcher()->dispatch('coreshop.' . $this->type . '.init', $event);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @param $type
     * @return bool|string
     */
    public function getClassForType($type)
    {
        if (array_key_exists($type, $this->types)) {
            return $this->types[$type];
        }

        return false;
    }

    /**
     * @return array
     */
    public function getTypeKeys()
    {
        return array_keys($this->types);
    }

    /**
     * add a new type
     *
     * @param $typeClass
     */
    public function addType($typeClass)
    {
        if (is_subclass_of($typeClass, $this->subclassOf)) {
            $this->types[$typeClass::getType()] = $typeClass;
        }
    }

    /**
     * add types
     *
     * @param array $types
     */
    public function addTypes($types)
    {
        foreach ($types as $type) {
            $this->addType($type);
        }
    }
}
