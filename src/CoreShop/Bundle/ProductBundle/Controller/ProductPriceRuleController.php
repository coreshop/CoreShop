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

namespace CoreShop\Bundle\ProductBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;

class ProductPriceRuleController extends ResourceController
{
    public function getConfigAction(Request $request)
    {
        $actions = $this->getConfigActions();
        $conditions = $this->getConfigConditions();

        return $this->viewHandler->handle(['actions' => array_keys($actions), 'conditions' => array_keys($conditions)]);
    }

    protected function getConfigActions()
    {
        return $this->getParameter('coreshop.product_price_rule.actions');
    }

    protected function getConfigConditions()
    {
        return $this->getParameter('coreshop.product_price_rule.conditions');
    }
}
