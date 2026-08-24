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

namespace CoreShop\Bundle\MessengerBundle\Messenger;

use Symfony\Component\Messenger\Envelope;

final class ChainFailedAtResolver implements FailedAtResolverInterface
{
    /**
     * @param iterable<FailedAtResolverInterface> $resolvers
     */
    public function __construct(
        private iterable $resolvers,
    ) {
    }

    public function resolve(Envelope $envelope): ?\DateTimeInterface
    {
        foreach ($this->resolvers as $resolver) {
            $failedAt = $resolver->resolve($envelope);

            if (null !== $failedAt) {
                return $failedAt;
            }
        }

        return null;
    }
}
