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

namespace CoreShop\Component\Rule\Condition;

/**
 * A condition checker whose outcome only depends on well-known context dimensions can be represented in
 * a precomputed price index. It declares the dimensions its result varies over. Checkers that do not
 * implement this interface make the rule non-indexable.
 */
interface IndexableConditionCheckerInterface
{
    public const string DIMENSION_STORE = 'store';

    public const string DIMENSION_CURRENCY = 'currency';

    public const string DIMENSION_CUSTOMER_GROUP = 'customer_group';

    public const string DIMENSION_COUNTRY = 'country';

    /** Rows per customer explicitly referenced by a rule, not per existing customer (see IndexableConditionValuesInterface). */
    public const string DIMENSION_CUSTOMER = 'customer';

    /** Rows per company explicitly referenced by a rule, combined with the customer groups. */
    public const string DIMENSION_COMPANY = 'company';

    /**
     * @return list<string> dimension names (store|currency|customer_group|country|customer|company); empty when the outcome is the same in every context
     */
    public function getPriceIndexDimensions(array $configuration): array;
}
