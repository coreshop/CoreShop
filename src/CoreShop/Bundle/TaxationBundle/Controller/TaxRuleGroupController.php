<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\TaxationBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;
use CoreShop\Component\Taxation\Repository\TaxRuleRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaxRuleGroupController extends ResourceController
{
    public function listRulesAction(Request $request, TaxRuleRepositoryInterface $taxRuleRepository): Response
    {
        /**
         * @var TaxRuleGroupInterface $ruleGroup
         */
        $ruleGroup = $this->findOr404($this->getParameterFromRequest($request, 'id'));
        $data = $taxRuleRepository->findByGroup($ruleGroup);

        return $this->viewHandler->handle($data);
    }
}
