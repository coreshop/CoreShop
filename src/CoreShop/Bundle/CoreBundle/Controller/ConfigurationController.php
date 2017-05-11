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

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use Symfony\Component\HttpFoundation\Request;

class ConfigurationController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return mixed|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function saveAllAction(Request $request)
    {
        $values = $this->decodeJson($request->get('values'));
        $values = array_htmlspecialchars($values);

        $diff = call_user_func_array([$this, 'array_diff_assoc_recursive'], $values);

        foreach ($values as $store => $storeValues) {
            $store = $this->get('coreshop.repository.store')->find($store);

            foreach ($storeValues as $key => $value) {
                $this->getConfigurationService()->removeForStore($key, $store);
            }
        }

        foreach ($values as $store => $storeValues) {
            $store = $this->get('coreshop.repository.store')->find($store);

            foreach ($storeValues as $key => $value) {
                if (array_key_exists($key, $diff)) {
                    $this->getConfigurationService()->setForStore($key, $value, $store);
                } else {
                    $this->getConfigurationService()->set($key, $value);
                }
            }
        }

        return $this->viewHandler->handle(['success' => true]);
    }

    /**
     * @return mixed|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAllAction()
    {
        $stores = $this->get('coreshop.repository.store')->findAll();
        $valueArray = [];

        foreach ($stores as $store) {
            $storeValues = [];

            $configurations = $this->repository->findBy(['store' => [$store, null]]);

            if (is_array($configurations)) {
                foreach ($configurations as $c) {
                    $storeValues[$c->getKey()] = $c->getData();
                }
            }

            $valueArray[$store->getId()] = $storeValues;
        }

        return $this->viewHandler->handle(['success' => true, 'data' => $valueArray]);
    }

    /**
     * @return ConfigurationServiceInterface
     */
    private function getConfigurationService()
    {
        return $this->get('coreshop.configuration.service');
    }

    /**
     * @return array
     */
    private function array_diff_assoc_recursive()
    {
        $args = func_get_args();
        $diff = [];
        foreach (array_shift($args) as $key => $val) {
            for ($i = 0, $j = 0, $tmp = [$val], $count = count($args); $i < $count; ++$i) {
                if (is_array($val)) {
                    if (!isset($args[$i][$key]) || !is_array($args[$i][$key]) || empty($args[$i][$key])) {
                        ++$j;
                    } else {
                        $tmp[] = $args[$i][$key];
                    }
                } elseif (!array_key_exists($key, $args[$i]) || $args[$i][$key] !== $val) {
                    ++$j;
                }
            }
            if (is_array($val)) {
                $tmp = call_user_func_array([$this, __METHOD__], $tmp);
                if (!empty($tmp)) {
                    $diff[$key] = $tmp;
                } elseif ($j == $count) {
                    $diff[$key] = $val;
                }
            } elseif ($j == $count && $count) {
                $diff[$key] = $val;
            }
        }

        return $diff;
    }
}
