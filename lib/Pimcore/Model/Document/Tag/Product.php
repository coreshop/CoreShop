<?php

namespace Pimcore\Model\Document\Tag;

use Pimcore\Model;
use Pimcore\Config;
use Pimcore\Model\Document;
use Pimcore\Model\Asset;
use Pimcore\Model\Object;
use Pimcore\Model\Element;

class Product extends Model\Document\Tag {

    /**
     * Contains the ID of the linked object
     *
     * @var integer
     */
    public $id;

    /**
     * Contains the object
     *
     * @var Document | Asset | Object\AbstractObject
     */
    public $o;


    /**
     * Contains the type
     *
     * @var string
     */
    public $type;


    /**
     * Contains the subtype
     *
     * @var string
     */
    public $subtype;

    /**
     * @see Document\Tag\TagInterface::getType
     * @return string
     */
    public function getType() {
        return "product";
    }

    /**
     * @see Document\Tag\TagInterface::getData
     * @return mixed
     */
    public function getData() {
        return array(
            "id" => $this->id,
            "type" => $this->getObjectType(),
            "subtype" => $this->subtype
        );
    }

    /**
     * Converts the data so it's suitable for the editmode
     *
     * @return mixed
     */
    public function getDataEditmode() {
        if ($this->o instanceof Element\ElementInterface) {
            return array(
                "id" => $this->id,
                "type" => $this->getObjectType(),
                "subtype" => $this->subtype
            );
        }
        return null;
    }

    /**
     * @see Document\Tag\TagInterface::frontend
     * @return string
     */
    public function frontend() {

        if ($this->o instanceof \Pimcore\Model\Object\CoreShopProduct) {
            if($this->getView())
                return $this->getView()->template("coreshop/product/preview.php", array("product" => $this->o));
        }
    }

    /**
     * @see Document\Tag\TagInterface::setDataFromResource
     * @param mixed $data
     * @return void
     */
    public function setDataFromResource($data) {

        $data = \Pimcore\Tool\Serialize::unserialize($data);

        $this->id = $data["id"];
        $this->type = $data["type"];
        $this->subtype = $data["subtype"];

        $this->setElement();
        return $this;
    }

    /**
     * @see Document\Tag\TagInterface::setDataFromEditmode
     * @param mixed $data
     * @return void
     */
    public function setDataFromEditmode($data) {

        $this->id = $data["id"];
        $this->type = $data["type"];
        $this->subtype = $data["subtype"];

        $this->setElement();
        return $this;
    }

    /**
     * Sets the element by the data stored for the object
     *
     * @return void
     */
    public function setElement() {
        $this->o = Element\Service::getElementById($this->type, $this->id);
        return $this;
    }

    /**
     * @return array
     */
    public function resolveDependencies() {

        $this->load();

        $dependencies = array();

        if ($this->o instanceof Element\ElementInterface) {

            $elementType = Element\Service::getElementType($this->o);
            $key = $elementType . "_" . $this->o->getId();

            $dependencies[$key] = array(
                "id" => $this->o->getId(),
                "type" => $elementType
            );
        }

        return $dependencies;
    }

    /**
     * get correct type of object as string
     * @param mixed $data
     * @return void
     */
    public function getObjectType($object = null) {

        $this->load();

        if (!$object) {
            $object = $this->o;
        }
        if($object instanceof Element\ElementInterface){
            return Element\Service::getType($object);
        } else {
            return false;
        }
    }


    /**
     * @return boolean
     */
    public function isEmpty () {

        $this->load();

        if($this->o instanceof Element\ElementInterface) {
            return false;
        }
        return true;
    }

    /**
     * @param Document\Webservice\Data\Document\Element $wsElement
     * @param null $idMapper
     * @throws \Exception
     */
    public function getFromWebserviceImport($wsElement, $idMapper = null) {
        $data = $wsElement->value;
        if ($data->id !==null) {

            $this->type = $data->type;
            $this->subtype = $data->subtype;
            if (is_numeric($this->id)) {
                if ($idMapper) {
                    $id = $idMapper->getMappedId($this->type, $this->id);
                }

                if ($this->type == "asset") {
                    $this->o = Asset::getById($id);
                    if(!$this->o instanceof Asset){
                        if ($idMapper && $idMapper->ignoreMappingFailures()) {
                            $idMapper->recordMappingFailure($this->getDocumentId(),$this->type, $this->id);
                        } else {
                            throw new \Exception("cannot get values from web service import - referenced asset with id [ ".$this->id." ] is unknown");
                        }
                    }
                } else if ($this->type == "document") {
                    $this->o = Document::getById($id);
                    if(!$this->o instanceof Document){
                        if ($idMapper && $idMapper->ignoreMappingFailures()) {
                            $idMapper->recordMappingFailure($this->getDocumentId(),$this->type, $this->id);
                        } else {
                            throw new \Exception("cannot get values from web service import - referenced document with id [ ".$this->id." ] is unknown");
                        }
                    }
                } else if ($this->type == "object") {
                    $this->o = Object::getById($id);
                    if(!$this->o instanceof Object\AbstractObject){
                        if ($idMapper && $idMapper->ignoreMappingFailures()) {
                            $idMapper->recordMappingFailure($this->getDocumentId(),$this->type, $this->id);
                        } else {
                            throw new \Exception("cannot get values from web service import - referenced object with id [ ".$this->id." ] is unknown");
                        }
                    }
                } else {
                    p_r($this);
                    throw new \Exception("cannot get values from web service import - type is not valid");
                }
            } else {
                throw new \Exception("cannot get values from web service import - id is not valid");
            }
        }
    }

    /**
     * @return bool
     */
    public function checkValidity() {
        $sane = true;
        if($this->id){
            $el = Element\Service::getElementById($this->type, $this->id);
            if(!$el instanceof Element\ElementInterface){
                $sane = false;
                \Logger::notice("Detected insane relation, removing reference to non existent ".$this->type." with id [".$this->id."]");
                $this->id = null;
                $this->type = null;
                $this->o=null;
                $this->subtype=null;
            }
        }
        return $sane;
    }

    /**
     * @return array
     */
    public function __sleep() {

        $finalVars = array();
        $parentVars = parent::__sleep();
        $blockedVars = array("o");
        foreach ($parentVars as $key) {
            if (!in_array($key, $blockedVars)) {
                $finalVars[] = $key;
            }
        }

        return $finalVars;
    }

    /**
     * this method is called by Document\Service::loadAllDocumentFields() to load all lazy loading fields
     *
     * @return void
     */
    public function load () {
        if(!$this->o) {
            $this->setElement();
        }
    }

    /**
     * @param int $id
     * @return Document\Tag\Renderlet
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * @param Asset|Document|Object $o
     * @return Document\Tag\Renderlet
     */
    public function setO($o)
    {
        $this->o = $o;
        return $this;
    }

    /**
     * @return Asset|Document|Object
     */
    public function getO()
    {
        return $this->o;
    }

    /**
     * @param string $subtype
     * @return Document\Tag\Renderlet
     */
    public function setSubtype($subtype)
    {
        $this->subtype = $subtype;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubtype()
    {
        return $this->subtype;
    }

    /**
     * Rewrites id from source to target, $idMapping contains
     * array(
     *  "document" => array(
     *      SOURCE_ID => TARGET_ID,
     *      SOURCE_ID => TARGET_ID
     *  ),
     *  "object" => array(...),
     *  "asset" => array(...)
     * )
     * @param array $idMapping
     * @return void
     */
    public function rewriteIds($idMapping) {
        $type = (string) $this->type;
        if($type && array_key_exists($this->type, $idMapping) and array_key_exists($this->getId(), $idMapping[$this->type])) {
            $this->setId($idMapping[$this->type][$this->getId()]);
            $this->setO(null);
        }
    }
}
