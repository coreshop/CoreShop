<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\OrderBundle\Grid\Column\Transformer;

use CoreShop\Bundle\WorkflowBundle\StateManager\WorkflowStateInfoManagerInterface;
use Pimcore\Bundle\StudioBackendBundle\Exception\Api\TransformerException;
use Pimcore\Bundle\StudioBackendBundle\Grid\Column\TransformerInterface;
use Pimcore\Bundle\StudioBackendBundle\Grid\Util\AdvancedValue;

final class OrderState implements TransformerInterface
{
    private const ALLOWED_WORKFLOWS = [
        'coreshop_order',
        'coreshop_order_payment',
        'coreshop_order_shipment',
        'coreshop_order_invoice',
    ];

    public function __construct(
        private readonly WorkflowStateInfoManagerInterface $workflowManager,
    ) {
    }

    /**
     * @throws TransformerException
     */
    public function transform(array $value, array $config): array
    {
        $workflow = $config['workflow'] ?? null;
        if (!is_string($workflow) || !in_array($workflow, self::ALLOWED_WORKFLOWS, true)) {
            throw new TransformerException(
                $this->getName(),
                sprintf(
                    'Config "workflow" is required and must be one of: %s.',
                    implode(', ', self::ALLOWED_WORKFLOWS),
                ),
            );
        }

        $highlightLabel = (bool) ($config['highlightLabel'] ?? false);
        $locale = is_string($config['locale'] ?? null) ? $config['locale'] : null;

        $results = [];
        foreach ($value as $val) {
            $results[] = $this->transformValue($val, $workflow, $highlightLabel, $locale);
        }

        return $results;
    }

    private function transformValue(
        AdvancedValue $val,
        string $workflow,
        bool $highlightLabel,
        ?string $locale,
    ): AdvancedValue {
        $data = $val->getValue();
        $fieldName = $val->getFieldName();

        if (!is_string($data) || $data === '') {
            return $val;
        }

        $state = $this->workflowManager->getStateInfo($workflow, $data, false, $locale);
        $label = $state['label'] ?? $data;

        if (!$highlightLabel || empty($state['color'])) {
            return new AdvancedValue('string', $label, $fieldName);
        }

        $rgb = $this->hex2rgb($state['color']);
        $opacity = $workflow === 'coreshop_order' ? '1' : '0.3';
        $textColor = $workflow === 'coreshop_order'
            ? $this->getContrastColor($rgb[0], $rgb[1], $rgb[2])
            : 'black';
        $backgroundColor = implode(',', $rgb);

        $html = sprintf(
            '<span class="rounded-color" style="background-color: rgba(%s, %s); color: %s;">%s</span>',
            $backgroundColor,
            $opacity,
            $textColor,
            $label,
        );

        return new AdvancedValue('string', $html, $fieldName);
    }

    /**
     * @return array{0:int,1:int,2:int}
     */
    private function hex2rgb(string $hex): array
    {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) === 3) {
            $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        return [(int) $r, (int) $g, (int) $b];
    }

    private function getContrastColor(int $r, int $g, int $b): string
    {
        $l1 = 0.2126 * (($r / 255) ** 2.2)
            + 0.7152 * (($g / 255) ** 2.2)
            + 0.0722 * (($b / 255) ** 2.2);

        return $l1 > 0.5 ? 'black' : 'white';
    }

    public function getName(): string
    {
        return 'CoreShop Order State';
    }

    public function getKey(): string
    {
        return 'coreshop_order_state';
    }

    public function getDescription(): string
    {
        return 'Renders the human-readable label of a CoreShop workflow state '
            . '(order / payment / shipment / invoice). Pick the workflow in the config.';
    }

    public function getConfigOptions(): array
    {
        return [
            'workflow' => [
                'type' => 'select',
                'label' => 'Workflow',
                'description' => 'Which CoreShop workflow this state belongs to.',
                'required' => true,
                'options' => [
                    ['value' => 'coreshop_order', 'label' => 'Order'],
                    ['value' => 'coreshop_order_payment', 'label' => 'Payment'],
                    ['value' => 'coreshop_order_shipment', 'label' => 'Shipment'],
                    ['value' => 'coreshop_order_invoice', 'label' => 'Invoice'],
                ],
            ],
            'highlightLabel' => [
                'type' => 'checkbox',
                'label' => 'Highlight Label',
                'description' => 'Wrap the state label in a colored span using the workflow state color.',
                'default' => false,
            ],
        ];
    }
}
