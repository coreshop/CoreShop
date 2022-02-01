<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\PimcoreBundle\CoreExtension;

use CoreShop\Component\Pimcore\DataObject\EditmodeHelper;
use Pimcore\Bundle\AdminBundle\Security\User\TokenStorageUserResolver;
use Pimcore\Bundle\AdminBundle\Security\User\User as UserProxy;
use Pimcore\Logger;
use Pimcore\Model\DataObject;
use Pimcore\Model\Element;
use Pimcore\Model\User;
use Pimcore\Tool;

final class EmbeddedClass extends DataObject\ClassDefinition\Data\ManyToManyRelation
{
    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopEmbeddedClass';

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
     * {@inheritdoc}
     */
    public function getObjectsAllowed()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getClasses()
    {
        return [['classes' => $this->getEmbeddedClassName()]];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        if (!is_array($data)) {
            return [];
        }

        $returnData = [];

        $i = 0;
        foreach ($data as $embeddedObject) {
            if (!$embeddedObject instanceof DataObject\Concrete) {
                continue;
            }

            if (!$embeddedObject->isAllowed('view')) {
                continue;
            }

            $objectData = [];

            $editmodeHelper = new EditmodeHelper();
            list('objectData' => $objectData['data'], 'metaData' => $objectData['metaData']) = $editmodeHelper->getDataForObject($embeddedObject);

            $objectData['id'] = $embeddedObject->getId();
            $objectData['general'] = [
                'index' => ++$i,
                'o_published' => $embeddedObject->getPublished(),
                'o_key' => $embeddedObject->getKey(),
                'o_id' => $embeddedObject->getId(),
                'o_modificationDate' => $embeddedObject->getModificationDate(),
                'o_creationDate' => $embeddedObject->getCreationDate(),
                'o_classId' => $embeddedObject->getClassId(),
                'o_className' => $embeddedObject->getClassName(),
                'o_locked' => $embeddedObject->getLocked(),
                'o_type' => $embeddedObject->getType(),
                'o_parentId' => $embeddedObject->getParentId(),
                'o_userOwner' => $embeddedObject->getUserOwner(),
                'o_userModification' => $embeddedObject->getUserModification(),
            ];

            $returnData[] = $objectData;
        }

        return $returnData;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        if (!is_array($data)) {
            return [];
        }

        $embeddedObjects = [];

        foreach ($data as $objectData) {
            $embeddedObject = null;

            if (array_key_exists('id', $objectData)) {
                $embeddedObject = $this->findInstance($objectData['id']);
            } elseif (array_key_exists('originalIndex', $objectData)) {
                $embeddedObject = $this->findInstanceByIndex($object, $objectData['originalIndex']);
            }

            if (!$embeddedObject instanceof DataObject\Concrete) {
                $embeddedObject = $this->createNewInstance();
                $embeddedObject->setKey(uniqid());
            }

            $embeddedObject->setIndex($objectData['currentIndex']);

            foreach ($objectData as $key => $value) {
                if (in_array($key, ['id', 'originalIndex', 'currentIndex'])) {
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
                        if (method_exists($fd, 'getOwnerClassName') && method_exists($fd, 'getOwnerFieldName')) {
                            $remoteClass = DataObject\ClassDefinition::getByName($fd->getOwnerClassName());
                            $relations = $embeddedObject->getRelationData(
                                $fd->getOwnerFieldName(),
                                false,
                                $remoteClass->getId()
                            );
                            $toAdd = $this->detectAddedRemoteOwnerRelations($relations, $value);
                            $toDelete = $this->detectDeletedRemoteOwnerRelations($relations, $value);
                            if (count($toAdd) > 0 or count($toDelete) > 0) {
                                $this->processRemoteOwnerRelations(
                                    $embeddedObject,
                                    $toDelete,
                                    $toAdd,
                                    $fd->getOwnerFieldName()
                                );
                            }
                        }
                    } else {
                        $embeddedObject->setValue($key, $fd->getDataFromEditmode($value, $embeddedObject));
                    }
                }
            }

            $embeddedObjects[] = $embeddedObject;
        }

        usort($embeddedObjects, function ($objectA, $objectB) {
            if ($objectA->getIndex() === $objectB->getIndex()) {
                return 0;
            }

            return ($objectA->getIndex() < $objectB->getIndex()) ? -1 : 1;
        });

        return $embeddedObjects;
    }

    public function preSetData($object, $data, $params = [])
    {
        $data = parent::preSetData($object, $data, $params);

        foreach ($data as $d) {
            if ($d instanceof DataObject\Concrete) {
                $d->setKey(uniqid());
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function preGetData($object, $params = [])
    {
        $data = $object->getObjectVar($this->getName());

        if (!is_array($data)) {
            $data = $this->load($object, ['force' => true]);
            $object->setObjectVar($this->getName(), $data);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function save($object, $params = [])
    {
        if (!$object instanceof DataObject\Concrete) {
            return;
        }

        $data = $this->getDataFromObjectParam($object, $params);

        $storedIds = [];

        foreach ($data as $embeddedObject) {
            $embeddedObject->setPublished($object->getPublished());
            $embeddedObject->setParent($object);

            $storedIds[] = $embeddedObject->getId();
        }

        $originalData = $object->getChildren();

        foreach ($originalData as $embeddedObject) {
            if (!in_array($embeddedObject->getId(), $storedIds)) {
                $embeddedObject->delete();
            }
        }

        foreach ($data as $index => $embeddedObject) {
            $embeddedObject->save();
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
     * @param array $relations
     * @param array $value
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
     * @param array $relations
     * @param array $value
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
     * Get user from user proxy object which is registered on security component.
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
     * @param int $id
     *
     * @return DataObject\Concrete
     */
    private function findInstance($id)
    {
        $fqcn = 'Pimcore\\Model\\DataObject\\' . $this->embeddedClassName;

        return $fqcn::getById($id);
    }

    /**
     * @param DataObject\Concrete $object
     * @param int                 $index
     *
     * @return mixed
     */
    private function findInstanceByIndex(DataObject\Concrete $object, $index)
    {
        $list = new DataObject\Listing();
        $list->setUnpublished(true);
        $list->setCondition('o_parentId = ? AND o_key = ?', [$object->getId(), $index]);
        $list->load();

        if ($list->count() >= 1) {
            return $list->getObjects()[0];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getLazyLoading()
    {
        return false;
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
}
