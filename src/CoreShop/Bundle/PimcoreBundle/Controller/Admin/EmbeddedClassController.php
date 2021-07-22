<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\PimcoreBundle\Controller\Admin;

use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class EmbeddedClassController extends AdminController
{
    public function getCustomLayoutsAction(Request $request)
    {
        $className = $request->get('className');
        $list = new DataObject\ClassDefinition\CustomLayout\Listing();

        $class = DataObject\ClassDefinition::getByName($className);

        if (!$class instanceof DataObject\ClassDefinition) {
            throw new NotFoundHttpException();
        }

        $list->setCondition('classId = ' . $list->quote($class->getId()));
        $list = $list->load();
        $result = [];
        /** @var DataObject\ClassDefinition\CustomLayout $item */
        foreach ($list as $item) {
            $result[] = [
                'id' => $item->getId(),
                'name' => $item->getName() . ' (ID: ' . $item->getId() . ')',
                'default' => $item->getDefault() ?: 0,
            ];
        }

        return $this->adminJson(['success' => true, 'data' => $result]);
    }

    public function getClassLayoutAction(Request $request)
    {
        $className = $request->get('className');
        $currentLayoutId = $request->get('layoutId', null) ?: null;

        $class = DataObject\ClassDefinition::getByName($className);

        if (!$class instanceof DataObject\ClassDefinition) {
            throw new NotFoundHttpException();
        }

        $fqcn = 'Pimcore\\Model\\DataObject\\' . ucfirst($class->getName());
        $tempInstance = new $fqcn();

        $validLayouts = DataObject\Service::getValidLayouts($tempInstance);

        if (is_null($currentLayoutId)) {
            foreach ($validLayouts as $checkDefaultLayout) {
                if ($checkDefaultLayout->getDefault()) {
                    $currentLayoutId = $checkDefaultLayout->getId();
                }
            }
        }

        if (!is_null($currentLayoutId)) {
            $customLayout = DataObject\ClassDefinition\CustomLayout::getById($currentLayoutId);
            $layout = $customLayout->getLayoutDefinitions();
        } else {
            $layout = $tempInstance->getClass()->getLayoutDefinitions();
        }

        $general = [
            'icon' => '',
            'iconCls' => '',
        ];

        if ($tempInstance->getElementAdminStyle()->getElementIcon()) {
            $general['icon'] = $tempInstance->getElementAdminStyle()->getElementIcon();
        }

        if ($tempInstance->getElementAdminStyle()->getElementIconClass()) {
            $general['iconCls'] = $tempInstance->getElementAdminStyle()->getElementIconClass();
        }

        return $this->adminJson([
            'layout' => $layout,
            'general' => $general,
        ]);
    }
}
