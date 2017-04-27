<?php

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use Symfony\Component\HttpFoundation\Request;

class ConfigurationController extends ResourceController
{
    public function saveAllAction(Request $request)
    {
        $values = $request->get('values');
        $systemValues = $request->get('systemValues');
        $values = array_htmlspecialchars($values);

        $diff = call_user_func_array([$this, "array_diff_assoc_recursive"], $values);

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

        foreach ($systemValues as $key => $value) {
             $this->getConfigurationService()->set($key, $value);
        }

        return $this->viewHandler->handle(['success' => true]);
    }

    /**
     * @return ConfigurationServiceInterface
     */
    private function getConfigurationService() {
        return $this->get('coreshop.configuration.service');
    }

    /**
     * @return array
     */
    private function array_diff_assoc_recursive()
    {
        $args = func_get_args();
        $diff =  [ ];
        foreach (array_shift($args) as $key => $val) {
            for ($i = 0, $j = 0, $tmp =  [ $val ], $count = count($args); $i < $count; $i++) {
                if (is_array($val)) {
                    if (!isset($args[$i][$key]) || !is_array($args[$i][$key]) || empty($args[$i][$key])) {
                        $j++;
                    } else {
                        $tmp[] = $args[$i][$key];
                    }
                } elseif (! array_key_exists($key, $args[$i]) || $args[$i][$key] !== $val) {
                    $j++;
                }
            }
            if (is_array($val)) {
                $tmp = call_user_func_array(array($this, __METHOD__), $tmp);
                if (! empty($tmp)) {
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
