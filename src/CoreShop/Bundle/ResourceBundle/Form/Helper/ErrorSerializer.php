<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ResourceBundle\Form\Helper;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

final class ErrorSerializer
{
    public function serializeErrorFromHandledForm(FormInterface $handledForm): array
    {
        $errors = [];

        /**
         * @var FormError $e
         */
        foreach ($handledForm->getErrors(true, true) as $e) {
            if ($e instanceof FormError) {
                $errorMessageTemplate = $e->getMessageTemplate();
                foreach ($e->getMessageParameters() as $key => $value) {
                    $errorMessageTemplate = str_replace($key, $value, $errorMessageTemplate);
                }

                $errors[] = sprintf('%s: %s', $e->getOrigin()->getConfig()->getName(), $errorMessageTemplate);
            }
        }

        return $errors;
    }
}
