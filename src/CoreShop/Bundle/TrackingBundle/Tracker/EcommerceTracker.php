<?php

namespace CoreShop\Bundle\TrackingBundle\Tracker;

use CoreShop\Bundle\TrackingBundle\Builder\ItemBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class EcommerceTracker
{
    /**
     * @var ItemBuilderInterface
     */
    protected $itemBuilder;

    /**
     * @var EngineInterface
     */
    protected $templatingEngine;

    /**
     * @var string
     */
    protected $templatePrefix;

    /**
     * @var string
     */
    protected $templateExtension;

    /**
     * EcommerceTracker constructor.
     *
     * @param EngineInterface      $templatingEngine
     * @param ItemBuilderInterface $itemBuilder
     * @param array                $options
     */
    public function __construct(
        EngineInterface $templatingEngine,
        ItemBuilderInterface $itemBuilder,
        array $options = []
    ) {
        $this->itemBuilder = $itemBuilder;
        $this->templatingEngine = $templatingEngine;

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->processOptions($resolver->resolve($options));
    }

    /**
     * @param array $options
     */
    protected function processOptions(array $options)
    {
        $this->templatePrefix = $options['template_prefix'];
        $this->templateExtension = $options['template_extension'];
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['template_prefix', 'template_extension']);
        $resolver->setDefaults([
            'template_extension' => 'twig'
        ]);

        $resolver->setAllowedTypes('template_prefix', 'string');
        $resolver->setAllowedTypes('template_extension', 'string');
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getTemplatePath(string $name)
    {
        return sprintf(
            '%s:%s.js.%s',
            $this->templatePrefix,
            $name,
            $this->templateExtension
        );
    }

    /**
     * @param string $name
     * @param array  $parameters
     * @return string
     */
    protected function renderTemplate(string $name, array $parameters): string
    {
        return $this->templatingEngine->render(
            $this->getTemplatePath($name),
            $parameters
        );
    }

    /**
     * Remove null values from an object, keep protected keys in any case
     *
     * @param array $data
     * @param array $protectedKeys
     *
     * @return array
     */
    protected function filterNullValues(array $data, array $protectedKeys = [])
    {
        $result = [];
        foreach ($data as $key => $value) {
            $isProtected = in_array($key, $protectedKeys);
            if (null !== $value || $isProtected) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
