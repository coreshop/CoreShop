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


namespace CoreShop\Bundle\AddressBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Twig\Environment;
use Twig\Error\Error;
use Twig\Error\SyntaxError;
use Twig\Sandbox\SecurityError;

/**
 * Ensures a country's address format only uses what the address format sandbox allows,
 * so invalid or disallowed Twig is rejected on save instead of failing at render time.
 */
final class AddressFormatValidator extends ConstraintValidator
{
    public function __construct(
        private Environment $twig,
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof AddressFormat) {
            throw new UnexpectedTypeException($constraint, AddressFormat::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        try {
            // The sandbox checks tags, filters and functions when the template is rendered.
            $this->twig->createTemplate($value)->render([]);
        } catch (SecurityError|SyntaxError $error) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%error%', $error->getRawMessage())
                ->addViolation()
            ;
        } catch (Error) {
            // Runtime errors caused by the empty sample context are not a problem of the format itself.
        }
    }
}
