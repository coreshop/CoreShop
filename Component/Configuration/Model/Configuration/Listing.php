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

namespace CoreShop\Component\Configuration\Model\Configuration;

use CoreShop\Component\Core\Model\Listing\JsonListingInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Pimcore\Model\Listing\JsonListing;

class Listing extends JsonListing implements JsonListingInterface
{
    /**
     * List of PriceRule.
     *
     * @var array
     */
    public $data = null;

    /**
     * @var string|\Zend_Locale
     */
    public $locale;

    /**
     * do not use the localized views for this list (in the case the class contains localized fields),
     * conditions on localized fields are not possible.
     *
     * @var bool
     */
    public $ignoreLocalizedFields = false;

    /**
     * List of valid order keys.
     *
     * @var array
     */
    public $validOrderKeys = [
        'id'
    ];

    /**
     * @var string
     */
    protected $modelClass;

    /**
     * @param string $modelClass
     */
    public function __construct($modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * Test if the passed key is valid.
     *
     * @param string $key
     *
     * @return bool
     */
    public function isValidOrderKey($key)
    {
        return true;
    }

    /**
     * @return array
     */
    public function getData()
    {
        if ($this->data === null) {
            $this->load();
        }

        return $this->data;
    }

    /**
     * @return ResourceInterface[]
     */
    public function load() {
        return $this->getDao()->load();
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Methods for \Zend_Paginator_Adapter_Interface.
     */

    /**
     * get total count.
     *
     * @return mixed
     */
    public function count()
    {
        return $this->getTotalCount();
    }

    /**
     * get all items.
     *
     * @param int $offset
     * @param int $itemCountPerPage
     *
     * @return mixed
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->setOffset($offset);
        $this->setLimit($itemCountPerPage);

        return $this->load();
    }

    /**
     * Get Paginator Adapter.
     *
     * @return $this
     */
    public function getPaginatorAdapter()
    {
        return $this;
    }

    /**
     * Set Locale.
     *
     * @param mixed $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Get Locale.
     *
     * @return string|\Zend_Locale
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set ignored localized fields.
     *
     * @param bool $ignoreLocalizedFields
     */
    public function setIgnoreLocalizedFields($ignoreLocalizedFields)
    {
        $this->ignoreLocalizedFields = $ignoreLocalizedFields;
    }

    /**
     * Get Ignored Localized Fields.
     *
     * @return bool
     */
    public function getIgnoreLocalizedFields()
    {
        return $this->ignoreLocalizedFields;
    }

    /**
     * @return string
     */
    public function getModelClass()
    {
        return $this->modelClass;
    }

    /**
     * Methods for Iterator.
     */

    /**
     * Rewind.
     */
    public function rewind()
    {
        $this->getData();
        reset($this->data);
    }

    /**
     * current.
     *
     * @return mixed
     */
    public function current()
    {
        $this->getData();
        $var = current($this->data);

        return $var;
    }

    /**
     * key.
     *
     * @return mixed
     */
    public function key()
    {
        $this->getData();
        $var = key($this->data);

        return $var;
    }

    /**
     * next.
     *
     * @return mixed
     */
    public function next()
    {
        $this->getData();
        $var = next($this->data);

        return $var;
    }

    /**
     * valid.
     *
     * @return bool
     */
    public function valid()
    {
        $this->getData();
        $var = $this->current() !== false;

        return $var;
    }
}
