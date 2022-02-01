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

namespace CoreShop\Bundle\ResourceBundle\Form\Helper;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

final class ErrorSerializer
{
    /**
     * @var TranslatorInterface
     */
    protected $trans;

    public function __construct(TranslatorInterface $trans)
    {
        $this->trans = $trans;
    }

    /**
     * @param FormInterface $handledForm
     *
     * @return array
     */
    public function serializeErrorFromHandledForm(FormInterface $handledForm)
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
                }
                else {
                    $errors[] = sprintf($errorMessageTemplate);
                }
            }
        }

        return $errors;
    }
}
