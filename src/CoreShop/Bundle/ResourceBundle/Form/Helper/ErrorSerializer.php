<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ResourceBundle\Form\Helper;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

final class ErrorSerializer
{
    /**
     * @param FormInterface $handledForm
     *
     * @return array
     */
    public function serializeErrorFromHandledForm(FormInterface $handledForm)
    {
        $errors = [];

        /**
         * @var FormError
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
