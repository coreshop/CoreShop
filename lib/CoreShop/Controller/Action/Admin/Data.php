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

namespace CoreShop\Controller\Action\Admin;

use CoreShop\Controller\Action\Admin;
use CoreShop\Exception;
use CoreShop\Model\AbstractModel;
use CoreShop\Model\Listing\AbstractListing;

/**
 * Class Data
 * @package CoreShop\Controller\Action\Admin
 */
class Data extends Admin
{
    /**
     * @var string
     */
    protected $permission;

    /**
     * @var array
     */
    protected $publicActions = ['list'];

    /**
     * @var string
     */
    protected $model;

    public function init()
    {
        parent::init();

        if (!in_array($this->getParam('action'), $this->getPublicActions())) {
            $this->checkPermission($this->getPermission());
        }

        if(!is_subclass_of($this->getModel(), AbstractModel::class)) {
            throw new Exception(sprintf("Class must be instanceof %s to be allowed as DataController", AbstractModel::class));
        }
    }

    /**
     * @return string
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * @return array
     */
    public function getPublicActions()
    {
        return $this->publicActions;
    }

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return AbstractModel
     */
    protected function create() {
        $model = $this->getModel();

        return new $model();
    }

    /**
     * @return AbstractListing
     */
    protected function getList() {
        $model = $this->getModel();

        return $model::getList();
    }

    /**
     * @param $id
     * @return AbstractModel|null
     */
    protected function getById($id) {
        $model = $this->getModel();

        return $model::getById($id);
    }

    /**
     * @param AbstractModel $model
     */
    protected function setDefaultValues(AbstractModel $model) {

    }

    /**
     * @param AbstractModel $model
     * @return array
     */
    protected function getReturnValues(AbstractModel $model) {
        $values = $model->getObjectVars();

        return $values;
    }

    /**
     * @param AbstractModel $model
     * @param $data
     */
    protected function prepareSave(AbstractModel $model, $data) {

    }

    /**
     * @param AbstractModel $model
     * @param $config
     * @return mixed
     */
    protected function prepareTreeNodeConfig(AbstractModel $model, $config) {
        return $config;
    }

    /**
     * @param AbstractModel $model
     *
     * @return array
     */
    protected function getTreeNodeConfig(AbstractModel $model)
    {
        $config = [
            'id' => $model->getId(),
            'text' => method_exists($model, "getName") ? $model->getName() : $model->getId(),
            'qtipCfg' => [
                'title' => 'ID: '.$model->getId(),
            ],
            'name' => method_exists($model, "getName") ? $model->getName() : $model->getId()
        ];

        return $this->prepareTreeNodeConfig($model, $config);
    }

    public function listAction()
    {
        $list = $this->getList();
        $list->setOrder('ASC');
        $list->setOrderKey('name');
        $list->load();

        $models = [];

        if (is_array($list->getData())) {
            foreach ($list->getData() as $model) {
                $models[] = $this->getTreeNodeConfig($model);
            }
        }
        $this->_helper->json($models);
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $model = $this->getById($id);

        if (is_a($model, $this->getModel())) {
            $this->_helper->json(['success' => true, 'data' => $this->getReturnValues($model)]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }

    public function saveAction()
    {
        $id = $this->getParam('id');
        $data = $this->getParam('data');
        $model = $this->getById($id);

        if ($data && is_a($model, $this->getModel())) {
            $data = \Zend_Json::decode($this->getParam('data'));

            $model->setValues($data);

            $this->prepareSave($model, $data);

            $model->save();

            $this->_helper->json(['success' => true, 'data' => $this->getReturnValues($model)]);
        } else {
            $this->_helper->json(['success' => false]);
        }
    }

    public function addAction()
    {
        $name = $this->getParam('name');

        if (strlen($name) <= 0) {
            $this->helper->json(['success' => false, 'message' => $this->getTranslator()->translate('Name must be set')]);
        } else {
            $model = $this->create();
            $model->setValues($this->getAllParams());

            $this->setDefaultValues($model);

            $model->save();

            $this->_helper->json(['success' => true, 'data' => $this->getReturnValues($model)]);
        }
    }

    public function deleteAction()
    {
        $id = $this->getParam('id');
        $model = $this->getById($id);

        if (is_a($model, $this->getModel())) {
            $model->delete();

            $this->_helper->json(['success' => true]);
        }

        $this->_helper->json(['success' => false]);
    }
}