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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Controller\Model;

use CoreShop\Bundle\CoreShopLegacyBundle\Controller\Admin\AdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HelperController
 *
 * @Route("/helper")
 */
class HelperController extends AdminController
{
    /**
     * @param Request $request
     * @return \Pimcore\Bundle\PimcoreAdminBundle\HttpFoundation\JsonResponse
     *
     * @Route("/get-order")
     */
    public function getOrderAction(Request $request)
    {
        $orderNumber = $request->get('orderNumber');

        if ($orderNumber) {
            $list = \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order::getList();
            $list->setCondition('orderNumber = ? OR orderNumber = ?', [$orderNumber, \CoreShop\Bundle\CoreShopLegacyBundle\Model\Order::getValidOrderNumber($orderNumber)]);

            $orders = $list->getObjects();

            if (count($orders) > 0) {
                return $this->json(['success' => true, 'id' => $orders[0]->getId()]);
            }
        }

        return $this->json(['success' => false]);
    }

    /**
     * @return \Pimcore\Bundle\PimcoreAdminBundle\HttpFoundation\JsonResponse
     *
     * @Route("/get-languages")
     */
    public function getLanguagesAction()
    {
        $locales = \Pimcore\Tool::getSupportedLocales();
        $languageOptions = [];
        $validLanguages = [];

        foreach ($locales as $short => $translation) {
            if (!empty($short)) {
                $languageOptions[] = [
                    'language' => $short,
                    'display' => $translation." ($short)",
                ];
                $validLanguages[] = $short;
            }
        }

        return $this->json(['languages' => $languageOptions]);
    }

    /**
     * @param Request $request
     * @return \Pimcore\Bundle\PimcoreAdminBundle\HttpFoundation\JsonResponse
     *
     * @Route("/get-nice-path")
     */
    public function getNicePathAction(Request $request)
    {
        $targets = \Zend_Json::decode($request->get("targets"));
        $result = [];

        foreach ($targets as $target) {
            $element = Pimcore\Model\Element\Service::getElementById($target['type'], $target['id']);

            if ($element instanceof Pimcore\Model\Element\AbstractElement) {
                $result[$element->getId()] = $element->getFullPath();
            }
        }

        return $this->json(["success" => true, "data" => $result]);
    }
}
