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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Controller\Admin;

use CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Listing\AbstractListing;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Data
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Controller\Admin
 */
class DataController extends AdminController
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

    /**
     * @param FilterControllerEvent $event
     * @throws \Exception
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if (!in_array($this->getActionName(), $this->getPublicActions())) {

            // permission check
            $access = $this->getUser()->getPermission($this->getPermission());

            if (!$access) {
                throw new \Exception(sprintf('this function requires "%s" permission!', $this->getPermission()));
            }
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

    /**
     * @Route("/list")
     */
    public function listAction(Request $request)
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
        return $this->json($models);
    }

    /**
     * @Route("/get")
     */
    public function getAction(Request $request)
    {
        $id = $request->get('id');
        $model = $this->getById($id);

        if (is_a($model, $this->getModel())) {
            return $this->json(['success' => true, 'data' => $this->getReturnValues($model)]);
        } else {
            return $this->json(['success' => false]);
        }
    }

    /**
     * @Route("/save")
     */
    public function saveAction(Request $request)
    {
        $id = $request->get('id');
        $data = $request->get('data');
        $model = $this->getById($id);

        if ($data && is_a($model, $this->getModel())) {
            $data = \Zend_Json::decode($request->get('data'));

            $model->setValues($data);

            $this->prepareSave($model, $data);

            $model->save();

            return $this->json(['success' => true, 'data' => $this->getReturnValues($model)]);
        } else {
            return $this->json(['success' => false]);
        }
    }
    /**
     * @Route("/add")
     */
    public function addAction(Request $request)
    {
        $name = $request->get('name');

        if (strlen($name) <= 0) {
            return $this->json(['success' => false, 'message' => $this->getTranslator()->translate('Name must be set')]);
        } else {
            $model = $this->create();
            $model->setValues($request->request->all());

            $this->setDefaultValues($model);

            $model->save();

            return $this->json(['success' => true, 'data' => $this->getReturnValues($model)]);
        }
    }

    /**
     * @Route("/delete")
     */
    public function deleteAction(Request $request)
    {
        $id = $request->get('id');
        $model = $this->getById($id);

        if (is_a($model, $this->getModel())) {
            $model->delete();

            return $this->json(['success' => true]);
        }

        return $this->json(['success' => false]);
    }
}