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

namespace CoreShop\Bundle\CoreBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AccessVoter extends Voter
{
    public function __construct(protected string $prefix = 'CORESHOP_')
    {
    }

    protected function supports(string $attribute, $subject)
    {
        return str_starts_with($attribute, $this->prefix);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        return true;
    }
}

