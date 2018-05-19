<?php

/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\PimcoreBundle\CoreExtension;

use CoreShop\Component\Pimcore\DataObject\EditmodeHelper;
use Pimcore\Bundle\AdminBundle\Security\User\TokenStorageUserResolver;
use Pimcore\Bundle\AdminBundle\Security\User\User as UserProxy;
use Pimcore\Logger;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Pimcore\Model\Element;
use Pimcore\Model\User;
use Pimcore\Tool;

final class EmbeddedClass extends DataObject\ClassDefinition\Data\Multihref
{
    /**
     * @var int
     */
    public $maxItems = 0;

    /**
     * @var string
     */
    public $embeddedClassName;

    /**
     * @var string
     */
    public $embeddedClassLayout;

    /**
     * @var string
     */
    public $embeddedObjectFolder;

    /**
     * @var string
     */
    public $embeddedObjectKey;

    private $valueBackupBeforeSave = null;

    /**
     * {@inheritdoc}
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        if (!is_array($data)) {
            return [];
        }

        $returnData = [];

        foreach ($data as $embeddedObject) {
            if (!$embeddedObject instanceof DataObject\Concrete) {
                continue;
            }

            if (!$embeddedObject->isAllowed('view')) {
                continue;
            }

            $objectData = [];

            $editmodeHelper = new EditmodeHelper();
            list("objectData" => $objectData['data'], 'metaData' => $objectData['metaData']) = $editmodeHelper->getDataForObject($embeddedObject);

            $objectData['id'] = $embeddedObject->getId();
            $objectData['general'] = [];

            $allowedKeys = ['o_published', 'o_key', 'o_id', 'o_modificationDate', 'o_creationDate', 'o_classId', 'o_className', 'o_locked', 'o_type', 'o_parentId', 'o_userOwner', 'o_userModification'];

            foreach (get_object_vars($embeddedObject) as $key => $value) {
                if (strstr($key, 'o_') && in_array($key, $allowedKeys)) {
                    $objectData['general'][$key] = $value;
                }
            }

            $returnData[] = $objectData;
        }

        return $returnData;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        $this->valueBackupBeforeSave = $this->getDataFromObjectParam($object, $params);

        if (!is_array($data)) {
            return [];
        }

        $embeddedObjects = [];

        foreach ($data as $objectData) {
            $embeddedObject = null;

            if (array_key_exists('id', $objectData)) {
                $embeddedObject = $this->findInstance($objectData['id']);
            }

            if (!$embeddedObject instanceof DataObject\Concrete) {
                $embeddedObject = $this->createNewInstance();
                $embeddedObject->setKey(uniqid());
            }

            foreach ($objectData as $key => $value) {
                if ($key === 'id') {
                    continue;
                }

                $fd = $embeddedObject->getClass()->getFieldDefinition($key);
                if ($fd) {
                    if ($fd instanceof DataObject\ClassDefinition\Data\Localizedfields) {
                        $user = Tool\Admin::getCurrentUser();
                        if (!$user->getAdmin()) {
                            $allowedLanguages = DataObject\Service::getLanguagePermissions($embeddedObject, $user, 'lEdit');
                            if (!is_null($allowedLanguages)) {
                                $allowedLanguages = array_keys($allowedLanguages);
                                $submittedLanguages = array_keys($data[$key]);
                                foreach ($submittedLanguages as $submittedLanguage) {
                                    if (!in_array($submittedLanguage, $allowedLanguages)) {
                                        unset($value[$submittedLanguage]);
                                    }
                                }
                            }
                        }
                    }

                    if (method_exists($fd, 'isRemoteOwner') and $fd->isRemoteOwner()) {
                        $remoteClass = DataObject\ClassDefinition::getByName($fd->getOwnerClassName());
                        $relations = $embeddedObject->getRelationData($fd->getOwnerFieldName(), false, $remoteClass->getId());
                        $toAdd = $this->detectAddedRemoteOwnerRelations($relations, $value);
                        $toDelete = $this->detectDeletedRemoteOwnerRelations($relations, $value);
                        if (count($toAdd) > 0 or count($toDelete) > 0) {
                            $this->processRemoteOwnerRelations($embeddedObject, $toDelete, $toAdd, $fd->getOwnerFieldName());
                        }
                    } else {
                        $embeddedObject->setValue($key, $fd->getDataFromEditmode($value, $embeddedObject));
                    }
                }
            }

            $embeddedObjects[] = $embeddedObject;
        }

        return $embeddedObjects;
    }

    /**
     * {@inheritdoc}
     */
    public function save($object, $params = [])
    {
        $data = $this->getDataFromObjectParam($object, $params);

        $params['force'] = true;

        $storedIds = [];

        foreach ($data as $embeddedObject) {
            $embeddedObject->setPublished($object->getPublished());
            $embeddedObject->setParent($object);
            $embeddedObject->save();

            $storedIds[] = $embeddedObject->getId();
        }

        if (is_array($this->valueBackupBeforeSave)) {
            foreach ($this->valueBackupBeforeSave as $embeddedObject) {
                if (!in_array($embeddedObject->getId(), $storedIds)) {
                    $embeddedObject->delete();
                }
            }
        }

        parent::save($object, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function checkValidity($data, $omitMandatoryCheck = false)
    {
        if (!$omitMandatoryCheck and $this->getMandatory() and empty($data)) {
            throw new Element\ValidationException('Empty mandatory field [ ' . $this->getName() . ' ]');
        }

        $allow = true;
        if (is_array($data)) {
            foreach ($data as $d) {
                if ($d instanceof DataObject\Concrete) {
                    $allow = $d->getClassName() === $this->embeddedClassName;
                }
                if (!$allow) {
                    throw new Element\ValidationException('Invalid multihref relation', null, null);
                }
            }

            if ($this->getMaxItems() && count($data) > $this->getMaxItems()) {
                throw new Element\ValidationException('Number of allowed relations in field `' . $this->getName() . '` exceeded (max. ' . $this->getMaxItems() . ')');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty($data)
    {
        if (!is_array($data) || count($data) === 0) {
            return true;
        }

        return false;
    }

    /**
     * @param  array $relations
     * @param  array $value
     *
     * @return array
     */
    protected function detectDeletedRemoteOwnerRelations($relations, $value)
    {
        $originals = [];
        $changed = [];
        foreach ($relations as $r) {
            $originals[] = $r['dest_id'];
        }
        if (is_array($value)) {
            foreach ($value as $row) {
                $changed[] = $row['id'];
            }
        }
        $diff = array_diff($originals, $changed);

        return $diff;
    }

    /**
     * @param  array $relations
     * @param  array $value
     *
     * @return array
     */
    protected function detectAddedRemoteOwnerRelations($relations, $value)
    {
        $originals = [];
        $changed = [];
        foreach ($relations as $r) {
            $originals[] = $r['dest_id'];
        }
        if (is_array($value)) {
            foreach ($value as $row) {
                $changed[] = $row['id'];
            }
        }
        $diff = array_diff($changed, $originals);

        return $diff;
    }

    protected function processRemoteOwnerRelations($object, $toDelete, $toAdd, $ownerFieldName)
    {
        $getter = 'get' . ucfirst($ownerFieldName);
        $setter = 'set' . ucfirst($ownerFieldName);

        foreach ($toDelete as $id) {
            $owner = DataObject::getById($id);
            //TODO: lock ?!
            if (method_exists($owner, $getter)) {
                $currentData = $owner->$getter();
                if (is_array($currentData)) {
                    for ($i = 0; $i < count($currentData); $i++) {
                        if ($currentData[$i]->getId() == $object->getId()) {
                            unset($currentData[$i]);
                            $owner->$setter($currentData);
                            $owner->setUserModification($this->getAdminUser()->getId());
                            $owner->save();
                            Logger::debug('Saved object id [ ' . $owner->getId() . ' ] by remote modification through [' . $object->getId() . '], Action: deleted [ ' . $object->getId() . " ] from [ $ownerFieldName]");
                            break;
                        }
                    }
                }
            }
        }

        foreach ($toAdd as $id) {
            $owner = DataObject::getById($id);
            //TODO: lock ?!
            if (method_exists($owner, $getter)) {
                $currentData = $owner->$getter();
                $currentData[] = $object;

                $owner->$setter($currentData);
                $owner->setUserModification($this->getAdminUser()->getId());
                $owner->save();
                Logger::debug('Saved object id [ ' . $owner->getId() . ' ] by remote modification through [' . $object->getId() . '], Action: added [ ' . $object->getId() . " ] to [ $ownerFieldName ]");
            }
        }
    }

    /**
     * Get user from user proxy object which is registered on security component
     *
     * @param bool $proxyUser Return the proxy user (UserInterface) instead of the pimcore model
     *
     * @return UserProxy|User
     */
    protected function getAdminUser($proxyUser = false)
    {
        $resolver = \Pimcore::getContainer()->get(TokenStorageUserResolver::class);

        if ($proxyUser) {
            return $resolver->getUserProxy();
        } else {
            return $resolver->getUser();
        }
    }

    /**
     * @return DataObject\Concrete
     */
    private function createNewInstance()
    {
        $fqcn = 'Pimcore\\Model\\DataObject\\' . $this->embeddedClassName;

        return new $fqcn();
    }

    /**
     * @param $id
     * @return DataObject\Concrete
     */
    private function findInstance($id)
    {
        $fqcn = 'Pimcore\\Model\\DataObject\\' . $this->embeddedClassName;

        return $fqcn::getById($id);
    }

    /**
     * @return int
     */
    public function getMaxItems()
    {
        return $this->maxItems;
    }

    /**
     * @param int $maxItems
     */
    public function setMaxItems($maxItems)
    {
        $this->maxItems = $maxItems;
    }

    /**
     * @return string
     */
    public function getEmbeddedClassName()
    {
        return $this->embeddedClassName;
    }

    /**
     * @param string $embeddedClassName
     */
    public function setEmbeddedClassName($embeddedClassName)
    {
        $this->embeddedClassName = $embeddedClassName;
    }

    /**
     * @return string
     */
    public function getEmbeddedClassLayout()
    {
        return $this->embeddedClassLayout;
    }

    /**
     * @param string $embeddedClassLayout
     */
    public function setEmbeddedClassLayout($embeddedClassLayout)
    {
        $this->embeddedClassLayout = $embeddedClassLayout;
    }

    /**
     * @return string
     */
    public function getEmbeddedObjectFolder()
    {
        return $this->embeddedObjectFolder;
    }

    /**
     * @param string $embeddedObjectFolder
     */
    public function setEmbeddedObjectFolder($embeddedObjectFolder)
    {
        $this->embeddedObjectFolder = $embeddedObjectFolder;
    }

    /**
     * @return string
     */
    public function getEmbeddedObjectKey()
    {
        return $this->embeddedObjectKey;
    }

    /**
     * @param string $embeddedObjectKey
     */
    public function setEmbeddedObjectKey($embeddedObjectKey)
    {
        $this->embeddedObjectKey = $embeddedObjectKey;
    }
}
