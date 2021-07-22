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

namespace CoreShop\Bundle\ShippingBundle\Form\Type;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CarrierChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    private $carrierRepository;

    /**
     * @param RepositoryInterface $carrierRepository
     */
    public function __construct(RepositoryInterface $carrierRepository)
    {
        $this->carrierRepository = $carrierRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    $carriers = $this->carrierRepository->findAll();

                    usort($carriers, function (CarrierInterface $a, CarrierInterface $b): int {
                        return $a->getIdentifier() <=> $b->getIdentifier();
                    });

                    return $carriers;
                },
                'choice_value' => 'id',
                'choice_label' => 'identifier',
                'choice_translation_domain' => false,
                'active' => true,
            ]);
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $description = [];
        $carriers = $form->getConfig()->getOption('choices');
        foreach ($carriers as $carrier) {
            if (!empty($carrier->getDescription())) {
                $description[$carrier->getId()] = $carrier->getDescription();
            }
        }
        $view->vars = array_merge($view->vars, [
            'choices_description' => $description,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_carrier_choice';
    }
}
