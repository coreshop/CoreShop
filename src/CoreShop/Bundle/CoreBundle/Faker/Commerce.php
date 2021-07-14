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

namespace CoreShop\Bundle\CoreBundle\Faker;

use Faker\Provider\Base;

class Commerce extends Base
{
    protected static $department = [
        'Books',
        'Movies',
        'Music',
        'Games',
        'Electronics',
        'Computers',
        'Home',
        'Garden',
        'Tools',
        'Grocery',
        'Health',
        'Beauty',
        'Toys',
        'Kids',
        'Baby',
        'Clothing',
        'Shoes',
        'Jewelry',
        'Sports',
        'Outdoors',
        'Automotive',
        'Industrial',
    ];
    protected static $productName = [
        'en' => [
            'adjective' => [
                'Small',
                'Ergonomic',
                'Rustic',
                'Intelligent',
                'Gorgeous',
                'Incredible',
                'Fantastic',
                'Practical',
                'Sleek',
                'Awesome',
                'Enormous',
                'Mediocre',
                'Synergistic',
                'Heavy Duty',
                'Lightweight',
                'Aerodynamic',
                'Durable',
            ],
            'material' => [
                'Steel',
                'Wooden',
                'Concrete',
                'Plastic',
                'Cotton',
                'Granite',
                'Rubber',
                'Leather',
                'Silk',
                'Wool',
                'Linen',
                'Marble',
                'Iron',
                'Bronze',
                'Copper',
                'Aluminum',
                'Paper',
            ],
            'product' => [
                'Chair',
                'Car',
                'Computer',
                'Gloves',
                'Pants',
                'Shirt',
                'Table',
                'Shoes',
                'Hat',
                'Plate',
                'Knife',
                'Bottle',
                'Coat',
                'Lamp',
                'Keyboard',
                'Bag',
                'Bench',
                'Clock',
                'Watch',
                'Wallet',
            ],
        ],
        'de' => [
            'adjective' => [
                'Klein',
                'Ergonomisch',
                'Rustikal',
                'Intelligent',
                'Herrlich',
                'Unglaublich',
                'Fantastisch',
                'Praktisch',
                'Glatt',
                'Genial',
                'Enorm',
                'Mittelmäßig',
                'Synergistisch',
                'Schwerlast',
                'Leicht',
                'Aerodynamisch',
                'Dauerhaft',
            ],
            'material' => [
                'Stahl',
                'Hölzern',
                'Beton',
                'Plastik',
                'Baumwolle',
                'Granit',
                'Gummi',
                'Leder',
                'Die Seide',
                'Wolle',
                'Leinen',
                'Marmor',
                'Eisen',
                'Bronze',
                'Kupfer',
                'Aluminium',
                'Papier',
            ],
            'product' => [
                'Stuhl',
                'Wagen',
                'Computer',
                'Handschuhe',
                'Hose',
                'Hemd',
                'Tabelle',
                'Schuhe',
                'Hut',
                'Teller',
                'Messer',
                'Flasche',
                'Mantel',
                'Lampe',
                'Tastatur',
                'Tasche',
                'Bank',
                'Uhr',
                'Uhr',
                'Brieftasche',
            ],
        ],
    ];
    protected static $promotionCode = [
        'adjective' => [
            'Amazing',
            'Awesome',
            'Cool',
            'Good',
            'Great',
            'Incredible',
            'Killer',
            'Premium',
            'Special',
            'Stellar',
            'Sweet',
        ],
        'noun' => ['Code', 'Deal', 'Discount', 'Price', 'Promo', 'Promotion', 'Sale', 'Savings'],
    ];

    public function promotionCode(int $digits = 6): string
    {
        return static::randomElement(static::$promotionCode['adjective'])
            .static::randomElement(static::$promotionCode['noun'])
            .$this->generator->randomNumber($digits, true);
    }

    public function department(int $max = 3, bool $fixedAmount = false): string
    {
        $categories = [];
        if (!$fixedAmount) {
            $max = mt_rand(1, $max);
        }
        $uniqueGenerator = $this->generator->unique(true);
        for ($i = 0; $i < $max; $i++) {
            $categories[] = $uniqueGenerator->category;
        }
        if (count($categories) >= 2) {
            $commaSeparatedCategories = implode(', ', array_slice($categories, 0, -1));
            $categories = [
                $commaSeparatedCategories,
                end($categories),
            ];
        }

        return implode(' & ', $categories);
    }

    public function category(): string
    {
        return static::randomElement(static::$department);
    }

    public function productName(?string $locale = null): string
    {
        return static::randomElement(static::$productName[$locale ?? 'en']['adjective'])
            .' '.static::randomElement(static::$productName[$locale ?? 'en']['material'])
            .' '.static::randomElement(static::$productName[$locale ?? 'en']['product']);
    }
}
