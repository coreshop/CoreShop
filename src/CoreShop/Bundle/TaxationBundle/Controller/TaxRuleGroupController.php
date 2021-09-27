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

declare(strict_types=1);

namespace CoreShop\Bundle\TaxationBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;
use CoreShop\Component\Taxation\Repository\TaxRuleRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaxRuleGroupController extends ResourceController
{
    public function listRulesAction(Request $request): Response
    {
        /**
         * @var TaxRuleGroupInterface $ruleGroup
         */
        $ruleGroup = $this->findOr404($request->get('id'));
        $data = $this->getTaxRulesRepository()->findByGroup($ruleGroup);

        return $this->viewHandler->handle($data);
    }

    protected function getTaxRulesRepository(): TaxRuleRepositoryInterface
    {
        return $this->get('coreshop.repository.tax_rule');
    }
}
