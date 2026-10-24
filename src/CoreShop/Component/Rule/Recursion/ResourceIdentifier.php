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

namespace CoreShop\Component\Rule\Recursion;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Doctrine\Persistence\Proxy;

/**
 * @internal
 */
final class ResourceIdentifier
{
    /**
     * Builds a key that identifies a resource independent of its PHP instance
     * (class + id; Doctrine proxies resolve to their entity class).
     */
    public static function identify(ResourceInterface $resource): string
    {
        $class = $resource instanceof Proxy ? get_parent_class($resource) : $resource::class;
        $id = $resource->getId();

        return sprintf('%s#%s', $class, null !== $id ? (string) $id : 'object-' . spl_object_id($resource));
    }

    public static function describeRule(RuleInterface $rule): string
    {
        $name = (string) $rule->getName();

        if ('' !== $name) {
            return $name;
        }

        $id = $rule->getId();

        return null !== $id ? '#' . $id : '#new';
    }

    /**
     * @param RuleInterface[] $rules
     */
    public static function describeRulePath(array $rules): string
    {
        return implode(' → ', array_map(self::describeRule(...), $rules));
    }
}
