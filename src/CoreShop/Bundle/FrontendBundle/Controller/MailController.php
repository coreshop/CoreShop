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

declare(strict_types=1);

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\FrontendBundle\TemplateConfigurator\TemplateConfiguratorInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MailController extends FrontendController
{
    public function mailAction(TemplateConfiguratorInterface $templateConfigurator): Response
    {
        return $this->renderTemplate($templateConfigurator->findTemplate('Mail/mail.html'));
    }

    public function orderConfirmationAction(
        Request $request,
        TemplateConfiguratorInterface $templateConfigurator
    ): Response {
        $order = $request->get('object');
        $viewParameters = [];

        if (!$this->editmode && $order instanceof OrderInterface) {
            $viewParameters['order'] = $order;
        }

        return $this->renderTemplate(
            $templateConfigurator->findTemplate('Mail/order-confirmation.html'),
            $viewParameters
        );
    }
}
