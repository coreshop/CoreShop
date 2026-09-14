<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Core\Product\Rule\Condition;

use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Customer\Model\CompanyInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use CoreShop\Component\Rule\Condition\IndexableConditionValuesInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

final class CompaniesConditionChecker implements ConditionCheckerInterface, IndexableConditionValuesInterface
{
    public function isValid(
        ResourceInterface $subject,
        RuleInterface $rule,
        array $configuration,
        array $params = [],
    ): bool {
        if (!array_key_exists('customer', $params) || !$params['customer'] instanceof CustomerInterface) {
            return false;
        }

        $company = $params['customer']->getCompany();

        if (!$company instanceof CompanyInterface) {
            return false;
        }

        return in_array($company->getId(), $configuration['companies'] ?? []);
    }

    public function getPriceIndexDimensions(array $configuration): array
    {
        return [self::DIMENSION_COMPANY];
    }

    public function getPriceIndexDimensionValues(array $configuration): array
    {
        return [self::DIMENSION_COMPANY => array_map('intval', $configuration['companies'] ?? [])];
    }
}
