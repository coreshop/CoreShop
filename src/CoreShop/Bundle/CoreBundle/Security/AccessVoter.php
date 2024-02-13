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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AccessVoter extends Voter
{
    public function __construct(
        protected string $prefix = 'CORESHOP_',
    ) {
    }

    /**
     * @phpstan-ignore-next-line
     */
    protected function supports(string $attribute, $subject)
    {
        return str_starts_with($attribute, $this->prefix);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        return true;
    }
}
