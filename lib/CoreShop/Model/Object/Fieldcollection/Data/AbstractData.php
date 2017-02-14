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

namespace CoreShop\Model\Object\Fieldcollection\Data;

use CoreShop\Exception;
use Pimcore\Model\Object\ClassDefinition\Data;
use Pimcore\Model\Object\Fieldcollection\Definition;
use Pimcore\Tool;

/**
 * Class AbstractData
 * @package CoreShop\Model\Object\Fieldcollection\Data
 */
class AbstractData extends \Pimcore\Model\Object\Fieldcollection\Data\AbstractData
{
    /**
     * Pimcore Object Class.
     *
     * @var string
     */
    public static $pimcoreClass = null;

    /**
     * get Pimcore implementation class.
     *
     * @return string
     */
    public static function getPimcoreObjectClass()
    {
        $class = get_called_class();

        if (\Pimcore::getDiContainer()->has($class)) {
            $class = \Pimcore::getDiContainer()->get($class);
        }

        return $class::$pimcoreClass;
    }

    /**
     * returns the class ID of the current object class.
     *
     * @return int
     */
    public static function getFieldCollectionType()
    {
        $v = get_class_vars(self::getPimcoreObjectClass());

        return $v['type'];
    }

    /**
     * Create new instance of Pimcore Object.
     *
     * @params $params directly sets values inside the object
     *
     * @throws Exception
     *
     * @return static
     */
    public static function create($params = [])
    {
        $pimcoreClass = self::getPimcoreObjectClass();

        if (Tool::classExists($pimcoreClass)) {
            $class = new $pimcoreClass();
            $class->setValues($params);

            return $class;
        }

        throw new Exception("Class $pimcoreClass not found");
    }

    /**
     * @return Data[]
     * @throws \Exception
     */
    public static function getMandatoryFields()
    {
        $class = self::getPimcoreObjectClass();
        $key = explode("\\", $class);
        $key = $key[count($key) - 1];

        $fieldCollectionDefinition = Definition::getByKey($key);
        $fields = $fieldCollectionDefinition->getFieldDefinitions();
        $mandatoryFields = [];

        foreach ($fields as $field) {
            if ($field instanceof Data) {
                if ($field->getMandatory()) {
                    $mandatoryFields[] = $field;
                }
            }
        }

        return $mandatoryFields;
    }

    /**
     * @param $data
     * @throws \Pimcore\Model\Element\ValidationException
     */
    public static function validate($data)
    {
        $mandatoryFields = self::getMandatoryFields();

        foreach ($mandatoryFields as $field) {
            $field->checkValidity($data[$field->getName()]);
        }
    }
}
