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

namespace CoreShop\Bundle\ResourceBundle\Form\Helper;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ErrorSerializer
{
    public function __construct(
        protected TranslatorInterface $trans,
    ) {
    }

    public function serializeErrorFromHandledForm(FormInterface $handledForm): array
    {
        $errors = [];

        /**
         * @var FormError $e
         */
        foreach ($handledForm->getErrors(true, true) as $e) {
            if ($e instanceof FormError) {
                $errorMessageTemplate = $e->getMessageTemplate();
                $errorMessageTemplate = $this->trans->trans($errorMessageTemplate);

                foreach ($e->getMessageParameters() as $key => $value) {
                    $errorMessageTemplate = str_replace($key, $value, $errorMessageTemplate);
                }

                if ($e->getOrigin()->getConfig()->getName()) {
                    $errors[] = sprintf('%s: %s', $e->getOrigin()->getConfig()->getName(), $errorMessageTemplate);
                } else {
                    $errors[] = $errorMessageTemplate;
                }
            }
        }

        return $errors;
    }
}
