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

namespace CoreShop\Bundle\IndexBundle\Worker\OpenSearchWorker\Mapping;

enum LanguageAnalyzer: string
{
    case ARABIC = 'arabic';
    case ARMENIAN = 'armenian';
    case BASQUE = 'basque';
    case BENGALI = 'bengali';
    case BRAZILIAN = 'brazilian';
    case BULGARIAN = 'bulgarian';
    case CATALAN = 'catalan';
    case CZECH = 'czech';
    case DANISH = 'danish';
    case DUTCH = 'dutch';
    case ENGLISH = 'english';
    case ESTONIAN = 'estonian';
    case FINNISH = 'finnish';
    case FRENCH = 'french';
    case GALICIAN = 'galician';
    case GERMAN = 'german';
    case GREEK = 'greek';
    case HINDI = 'hindi';
    case HUNGARIAN = 'hungarian';
    case INDONESIAN = 'indonesian';
    case IRISH = 'irish';
    case ITALIAN = 'italian';
    case LATVIAN = 'latvian';
    case LITHUANIAN = 'lithuanian';
    case NORWEGIAN = 'norwegian';
    case PERSIAN = 'persian';
    case PORTUGUESE = 'portuguese';
    case ROMANIAN = 'romanian';
    case RUSSIAN = 'russian';
    case SORANI = 'sorani';
    case SPANISH = 'spanish';
    case SWEDISH = 'swedish';
    case THAI = 'thai';
    case TURKISH = 'turkish';

    public static function fromLocale(string $locale): ?self
    {
        $map = [
            'ar' => self::ARABIC,
            'hy' => self::ARMENIAN,
            'eu' => self::BASQUE,
            'bn' => self::BENGALI,
            'pt_BR' => self::BRAZILIAN,
            'bg' => self::BULGARIAN,
            'ca' => self::CATALAN,
            'cs' => self::CZECH,
            'da' => self::DANISH,
            'nl' => self::DUTCH,
            'en' => self::ENGLISH,
            'et' => self::ESTONIAN,
            'fi' => self::FINNISH,
            'fr' => self::FRENCH,
            'gl' => self::GALICIAN,
            'de' => self::GERMAN,
            'el' => self::GREEK,
            'hi' => self::HINDI,
            'hu' => self::HUNGARIAN,
            'id' => self::INDONESIAN,
            'ga' => self::IRISH,
            'it' => self::ITALIAN,
            'lv' => self::LATVIAN,
            'lt' => self::LITHUANIAN,
            'no' => self::NORWEGIAN,
            'fa' => self::PERSIAN,
            'pt' => self::PORTUGUESE,
            'ro' => self::ROMANIAN,
            'ru' => self::RUSSIAN,
            'ku' => self::SORANI,
            'es' => self::SPANISH,
            'sv' => self::SWEDISH,
            'th' => self::THAI,
            'tr' => self::TURKISH,
        ];

        // Try to find an exact match first
        if (isset($map[$locale])) {
            return $map[$locale];
        }

        // Fallback to language part only (e.g. 'en' from 'en_US')
        if (\str_contains($locale, '_')) {
            $primary = \Locale::getPrimaryLanguage($locale);

            if (isset($map[$primary])) {
                return $map[$primary];
            }
        }

        return null;
    }
}
