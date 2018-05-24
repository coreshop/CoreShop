<?php

namespace CoreShop\Bundle\CoreBundle\Migrations;

use CoreShop\Component\Pimcore\DataObject\ClassUpdate;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Version20180408091435 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $localeField = [
            'fieldtype' => 'language',
            'onlySystemLanguages' => false,
            'options' => [
                [
                    'key' => 'Afrikaans',
                    'value' => 'af'
                ],
                [
                    'key' => 'Afrikaans (Namibia)',
                    'value' => 'af_NA'
                ],
                [
                    'key' => 'Afrikaans (South Africa)',
                    'value' => 'af_ZA'
                ],
                [
                    'key' => 'Aghem',
                    'value' => 'agq'
                ],
                [
                    'key' => 'Aghem (Cameroon)',
                    'value' => 'agq_CM'
                ],
                [
                    'key' => 'Akan',
                    'value' => 'ak'
                ],
                [
                    'key' => 'Akan (Ghana)',
                    'value' => 'ak_GH'
                ],
                [
                    'key' => 'Albanian',
                    'value' => 'sq'
                ],
                [
                    'key' => 'Albanian (Albania)',
                    'value' => 'sq_AL'
                ],
                [
                    'key' => 'Albanian (Kosovo)',
                    'value' => 'sq_XK'
                ],
                [
                    'key' => 'Albanian (Macedonia)',
                    'value' => 'sq_MK'
                ],
                [
                    'key' => 'Amharic',
                    'value' => 'am'
                ],
                [
                    'key' => 'Amharic (Ethiopia)',
                    'value' => 'am_ET'
                ],
                [
                    'key' => 'Arabic',
                    'value' => 'ar'
                ],
                [
                    'key' => 'Arabic (Algeria)',
                    'value' => 'ar_DZ'
                ],
                [
                    'key' => 'Arabic (Bahrain)',
                    'value' => 'ar_BH'
                ],
                [
                    'key' => 'Arabic (Chad)',
                    'value' => 'ar_TD'
                ],
                [
                    'key' => 'Arabic (Comoros)',
                    'value' => 'ar_KM'
                ],
                [
                    'key' => 'Arabic (Djibouti)',
                    'value' => 'ar_DJ'
                ],
                [
                    'key' => 'Arabic (Egypt)',
                    'value' => 'ar_EG'
                ],
                [
                    'key' => 'Arabic (Eritrea)',
                    'value' => 'ar_ER'
                ],
                [
                    'key' => 'Arabic (Iraq)',
                    'value' => 'ar_IQ'
                ],
                [
                    'key' => 'Arabic (Israel)',
                    'value' => 'ar_IL'
                ],
                [
                    'key' => 'Arabic (Jordan)',
                    'value' => 'ar_JO'
                ],
                [
                    'key' => 'Arabic (Kuwait)',
                    'value' => 'ar_KW'
                ],
                [
                    'key' => 'Arabic (Lebanon)',
                    'value' => 'ar_LB'
                ],
                [
                    'key' => 'Arabic (Libya)',
                    'value' => 'ar_LY'
                ],
                [
                    'key' => 'Arabic (Mauritania)',
                    'value' => 'ar_MR'
                ],
                [
                    'key' => 'Arabic (Morocco)',
                    'value' => 'ar_MA'
                ],
                [
                    'key' => 'Arabic (Oman)',
                    'value' => 'ar_OM'
                ],
                [
                    'key' => 'Arabic (Palestinian Territories)',
                    'value' => 'ar_PS'
                ],
                [
                    'key' => 'Arabic (Qatar)',
                    'value' => 'ar_QA'
                ],
                [
                    'key' => 'Arabic (Saudi Arabia)',
                    'value' => 'ar_SA'
                ],
                [
                    'key' => 'Arabic (Somalia)',
                    'value' => 'ar_SO'
                ],
                [
                    'key' => 'Arabic (South Sudan)',
                    'value' => 'ar_SS'
                ],
                [
                    'key' => 'Arabic (Sudan)',
                    'value' => 'ar_SD'
                ],
                [
                    'key' => 'Arabic (Syria)',
                    'value' => 'ar_SY'
                ],
                [
                    'key' => 'Arabic (Tunisia)',
                    'value' => 'ar_TN'
                ],
                [
                    'key' => 'Arabic (United Arab Emirates)',
                    'value' => 'ar_AE'
                ],
                [
                    'key' => 'Arabic (Western Sahara)',
                    'value' => 'ar_EH'
                ],
                [
                    'key' => 'Arabic (World)',
                    'value' => 'ar_001'
                ],
                [
                    'key' => 'Arabic (Yemen)',
                    'value' => 'ar_YE'
                ],
                [
                    'key' => 'Armenian',
                    'value' => 'hy'
                ],
                [
                    'key' => 'Armenian (Armenia)',
                    'value' => 'hy_AM'
                ],
                [
                    'key' => 'Assamese',
                    'value' => 'as'
                ],
                [
                    'key' => 'Assamese (India)',
                    'value' => 'as_IN'
                ],
                [
                    'key' => 'Asu',
                    'value' => 'asa'
                ],
                [
                    'key' => 'Asu (Tanzania)',
                    'value' => 'asa_TZ'
                ],
                [
                    'key' => 'Azerbaijani',
                    'value' => 'az_Cyrl'
                ],
                [
                    'key' => 'Azerbaijani',
                    'value' => 'az'
                ],
                [
                    'key' => 'Azerbaijani',
                    'value' => 'az_Latn'
                ],
                [
                    'key' => 'Azerbaijani (Azerbaijan)',
                    'value' => 'az_Cyrl_AZ'
                ],
                [
                    'key' => 'Azerbaijani (Azerbaijan)',
                    'value' => 'az_Latn_AZ'
                ],
                [
                    'key' => 'Bafia',
                    'value' => 'ksf'
                ],
                [
                    'key' => 'Bafia (Cameroon)',
                    'value' => 'ksf_CM'
                ],
                [
                    'key' => 'Bambara',
                    'value' => 'bm'
                ],
                [
                    'key' => 'Bambara (Mali)',
                    'value' => 'bm_ML'
                ],
                [
                    'key' => 'Basaa',
                    'value' => 'bas'
                ],
                [
                    'key' => 'Basaa (Cameroon)',
                    'value' => 'bas_CM'
                ],
                [
                    'key' => 'Basque',
                    'value' => 'eu'
                ],
                [
                    'key' => 'Basque (Spain)',
                    'value' => 'eu_ES'
                ],
                [
                    'key' => 'Belarusian',
                    'value' => 'be'
                ],
                [
                    'key' => 'Belarusian (Belarus)',
                    'value' => 'be_BY'
                ],
                [
                    'key' => 'Bemba',
                    'value' => 'bem'
                ],
                [
                    'key' => 'Bemba (Zambia)',
                    'value' => 'bem_ZM'
                ],
                [
                    'key' => 'Bena',
                    'value' => 'bez'
                ],
                [
                    'key' => 'Bena (Tanzania)',
                    'value' => 'bez_TZ'
                ],
                [
                    'key' => 'Bengali',
                    'value' => 'bn'
                ],
                [
                    'key' => 'Bengali (Bangladesh)',
                    'value' => 'bn_BD'
                ],
                [
                    'key' => 'Bengali (India)',
                    'value' => 'bn_IN'
                ],
                [
                    'key' => 'Bodo',
                    'value' => 'brx'
                ],
                [
                    'key' => 'Bodo (India)',
                    'value' => 'brx_IN'
                ],
                [
                    'key' => 'Bosnian',
                    'value' => 'bs_Latn'
                ],
                [
                    'key' => 'Bosnian',
                    'value' => 'bs'
                ],
                [
                    'key' => 'Bosnian',
                    'value' => 'bs_Cyrl'
                ],
                [
                    'key' => 'Bosnian (Bosnia & Herzegovina)',
                    'value' => 'bs_Latn_BA'
                ],
                [
                    'key' => 'Bosnian (Bosnia & Herzegovina)',
                    'value' => 'bs_Cyrl_BA'
                ],
                [
                    'key' => 'Breton',
                    'value' => 'br'
                ],
                [
                    'key' => 'Breton (France)',
                    'value' => 'br_FR'
                ],
                [
                    'key' => 'Bulgarian',
                    'value' => 'bg'
                ],
                [
                    'key' => 'Bulgarian (Bulgaria)',
                    'value' => 'bg_BG'
                ],
                [
                    'key' => 'Burmese',
                    'value' => 'my'
                ],
                [
                    'key' => 'Burmese (Myanmar (Burma))',
                    'value' => 'my_MM'
                ],
                [
                    'key' => 'Catalan',
                    'value' => 'ca'
                ],
                [
                    'key' => 'Catalan (Andorra)',
                    'value' => 'ca_AD'
                ],
                [
                    'key' => 'Catalan (France)',
                    'value' => 'ca_FR'
                ],
                [
                    'key' => 'Catalan (Italy)',
                    'value' => 'ca_IT'
                ],
                [
                    'key' => 'Catalan (Spain)',
                    'value' => 'ca_ES'
                ],
                [
                    'key' => 'Central Atlas Tamazight',
                    'value' => 'tzm'
                ],
                [
                    'key' => 'Central Atlas Tamazight (Morocco)',
                    'value' => 'tzm_MA'
                ],
                [
                    'key' => 'Chechen',
                    'value' => 'ce'
                ],
                [
                    'key' => 'Chechen (Russia)',
                    'value' => 'ce_RU'
                ],
                [
                    'key' => 'Cherokee',
                    'value' => 'chr'
                ],
                [
                    'key' => 'Cherokee (United States)',
                    'value' => 'chr_US'
                ],
                [
                    'key' => 'Chiga',
                    'value' => 'cgg'
                ],
                [
                    'key' => 'Chiga (Uganda)',
                    'value' => 'cgg_UG'
                ],
                [
                    'key' => 'Chinese',
                    'value' => 'zh_Hans'
                ],
                [
                    'key' => 'Chinese',
                    'value' => 'zh'
                ],
                [
                    'key' => 'Chinese',
                    'value' => 'zh_Hant'
                ],
                [
                    'key' => 'Chinese (China)',
                    'value' => 'zh_Hans_CN'
                ],
                [
                    'key' => 'Chinese (Hong Kong SAR China)',
                    'value' => 'zh_Hant_HK'
                ],
                [
                    'key' => 'Chinese (Hong Kong SAR China)',
                    'value' => 'zh_Hans_HK'
                ],
                [
                    'key' => 'Chinese (Macau SAR China)',
                    'value' => 'zh_Hant_MO'
                ],
                [
                    'key' => 'Chinese (Macau SAR China)',
                    'value' => 'zh_Hans_MO'
                ],
                [
                    'key' => 'Chinese (Singapore)',
                    'value' => 'zh_Hans_SG'
                ],
                [
                    'key' => 'Chinese (Taiwan)',
                    'value' => 'zh_Hant_TW'
                ],
                [
                    'key' => 'Colognian',
                    'value' => 'ksh'
                ],
                [
                    'key' => 'Colognian (Germany)',
                    'value' => 'ksh_DE'
                ],
                [
                    'key' => 'Cornish',
                    'value' => 'kw'
                ],
                [
                    'key' => 'Cornish (United Kingdom)',
                    'value' => 'kw_GB'
                ],
                [
                    'key' => 'Croatian',
                    'value' => 'hr'
                ],
                [
                    'key' => 'Croatian (Bosnia & Herzegovina)',
                    'value' => 'hr_BA'
                ],
                [
                    'key' => 'Croatian (Croatia)',
                    'value' => 'hr_HR'
                ],
                [
                    'key' => 'Czech',
                    'value' => 'cs'
                ],
                [
                    'key' => 'Czech (Czech Republic)',
                    'value' => 'cs_CZ'
                ],
                [
                    'key' => 'Danish',
                    'value' => 'da'
                ],
                [
                    'key' => 'Danish (Denmark)',
                    'value' => 'da_DK'
                ],
                [
                    'key' => 'Danish (Greenland)',
                    'value' => 'da_GL'
                ],
                [
                    'key' => 'Duala',
                    'value' => 'dua'
                ],
                [
                    'key' => 'Duala (Cameroon)',
                    'value' => 'dua_CM'
                ],
                [
                    'key' => 'Dutch',
                    'value' => 'nl'
                ],
                [
                    'key' => 'Dutch (Aruba)',
                    'value' => 'nl_AW'
                ],
                [
                    'key' => 'Dutch (Belgium)',
                    'value' => 'nl_BE'
                ],
                [
                    'key' => 'Dutch (Caribbean Netherlands)',
                    'value' => 'nl_BQ'
                ],
                [
                    'key' => 'Dutch (Cura\u00e7ao)',
                    'value' => 'nl_CW'
                ],
                [
                    'key' => 'Dutch (Netherlands)',
                    'value' => 'nl_NL'
                ],
                [
                    'key' => 'Dutch (Sint Maarten)',
                    'value' => 'nl_SX'
                ],
                [
                    'key' => 'Dutch (Suriname)',
                    'value' => 'nl_SR'
                ],
                [
                    'key' => 'Dzongkha',
                    'value' => 'dz'
                ],
                [
                    'key' => 'Dzongkha (Bhutan)',
                    'value' => 'dz_BT'
                ],
                [
                    'key' => 'Embu',
                    'value' => 'ebu'
                ],
                [
                    'key' => 'Embu (Kenya)',
                    'value' => 'ebu_KE'
                ],
                [
                    'key' => 'English',
                    'value' => 'en'
                ],
                [
                    'key' => 'English (American Samoa)',
                    'value' => 'en_AS'
                ],
                [
                    'key' => 'English (Anguilla)',
                    'value' => 'en_AI'
                ],
                [
                    'key' => 'English (Antigua & Barbuda)',
                    'value' => 'en_AG'
                ],
                [
                    'key' => 'English (Australia)',
                    'value' => 'en_AU'
                ],
                [
                    'key' => 'English (Austria)',
                    'value' => 'en_AT'
                ],
                [
                    'key' => 'English (Bahamas)',
                    'value' => 'en_BS'
                ],
                [
                    'key' => 'English (Barbados)',
                    'value' => 'en_BB'
                ],
                [
                    'key' => 'English (Belgium)',
                    'value' => 'en_BE'
                ],
                [
                    'key' => 'English (Belize)',
                    'value' => 'en_BZ'
                ],
                [
                    'key' => 'English (Bermuda)',
                    'value' => 'en_BM'
                ],
                [
                    'key' => 'English (Botswana)',
                    'value' => 'en_BW'
                ],
                [
                    'key' => 'English (British Indian Ocean Territory)',
                    'value' => 'en_IO'
                ],
                [
                    'key' => 'English (British Virgin Islands)',
                    'value' => 'en_VG'
                ],
                [
                    'key' => 'English (Burundi)',
                    'value' => 'en_BI'
                ],
                [
                    'key' => 'English (Cameroon)',
                    'value' => 'en_CM'
                ],
                [
                    'key' => 'English (Canada)',
                    'value' => 'en_CA'
                ],
                [
                    'key' => 'English (Cayman Islands)',
                    'value' => 'en_KY'
                ],
                [
                    'key' => 'English (Christmas Island)',
                    'value' => 'en_CX'
                ],
                [
                    'key' => 'English (Cocos (Keeling) Islands)',
                    'value' => 'en_CC'
                ],
                [
                    'key' => 'English (Cook Islands)',
                    'value' => 'en_CK'
                ],
                [
                    'key' => 'English (Cyprus)',
                    'value' => 'en_CY'
                ],
                [
                    'key' => 'English (Denmark)',
                    'value' => 'en_DK'
                ],
                [
                    'key' => 'English (Diego Garcia)',
                    'value' => 'en_DG'
                ],
                [
                    'key' => 'English (Dominica)',
                    'value' => 'en_DM'
                ],
                [
                    'key' => 'English (Eritrea)',
                    'value' => 'en_ER'
                ],
                [
                    'key' => 'English (Europe)',
                    'value' => 'en_150'
                ],
                [
                    'key' => 'English (Falkland Islands)',
                    'value' => 'en_FK'
                ],
                [
                    'key' => 'English (Fiji)',
                    'value' => 'en_FJ'
                ],
                [
                    'key' => 'English (Finland)',
                    'value' => 'en_FI'
                ],
                [
                    'key' => 'English (Gambia)',
                    'value' => 'en_GM'
                ],
                [
                    'key' => 'English (Germany)',
                    'value' => 'en_DE'
                ],
                [
                    'key' => 'English (Ghana)',
                    'value' => 'en_GH'
                ],
                [
                    'key' => 'English (Gibraltar)',
                    'value' => 'en_GI'
                ],
                [
                    'key' => 'English (Grenada)',
                    'value' => 'en_GD'
                ],
                [
                    'key' => 'English (Guam)',
                    'value' => 'en_GU'
                ],
                [
                    'key' => 'English (Guernsey)',
                    'value' => 'en_GG'
                ],
                [
                    'key' => 'English (Guyana)',
                    'value' => 'en_GY'
                ],
                [
                    'key' => 'English (Hong Kong SAR China)',
                    'value' => 'en_HK'
                ],
                [
                    'key' => 'English (India)',
                    'value' => 'en_IN'
                ],
                [
                    'key' => 'English (Ireland)',
                    'value' => 'en_IE'
                ],
                [
                    'key' => 'English (Isle of Man)',
                    'value' => 'en_IM'
                ],
                [
                    'key' => 'English (Israel)',
                    'value' => 'en_IL'
                ],
                [
                    'key' => 'English (Jamaica)',
                    'value' => 'en_JM'
                ],
                [
                    'key' => 'English (Jersey)',
                    'value' => 'en_JE'
                ],
                [
                    'key' => 'English (Kenya)',
                    'value' => 'en_KE'
                ],
                [
                    'key' => 'English (Kiribati)',
                    'value' => 'en_KI'
                ],
                [
                    'key' => 'English (Lesotho)',
                    'value' => 'en_LS'
                ],
                [
                    'key' => 'English (Liberia)',
                    'value' => 'en_LR'
                ],
                [
                    'key' => 'English (Macau SAR China)',
                    'value' => 'en_MO'
                ],
                [
                    'key' => 'English (Madagascar)',
                    'value' => 'en_MG'
                ],
                [
                    'key' => 'English (Malawi)',
                    'value' => 'en_MW'
                ],
                [
                    'key' => 'English (Malaysia)',
                    'value' => 'en_MY'
                ],
                [
                    'key' => 'English (Malta)',
                    'value' => 'en_MT'
                ],
                [
                    'key' => 'English (Marshall Islands)',
                    'value' => 'en_MH'
                ],
                [
                    'key' => 'English (Mauritius)',
                    'value' => 'en_MU'
                ],
                [
                    'key' => 'English (Micronesia)',
                    'value' => 'en_FM'
                ],
                [
                    'key' => 'English (Montserrat)',
                    'value' => 'en_MS'
                ],
                [
                    'key' => 'English (Namibia)',
                    'value' => 'en_NA'
                ],
                [
                    'key' => 'English (Nauru)',
                    'value' => 'en_NR'
                ],
                [
                    'key' => 'English (Netherlands)',
                    'value' => 'en_NL'
                ],
                [
                    'key' => 'English (New Zealand)',
                    'value' => 'en_NZ'
                ],
                [
                    'key' => 'English (Nigeria)',
                    'value' => 'en_NG'
                ],
                [
                    'key' => 'English (Niue)',
                    'value' => 'en_NU'
                ],
                [
                    'key' => 'English (Norfolk Island)',
                    'value' => 'en_NF'
                ],
                [
                    'key' => 'English (Northern Mariana Islands)',
                    'value' => 'en_MP'
                ],
                [
                    'key' => 'English (Pakistan)',
                    'value' => 'en_PK'
                ],
                [
                    'key' => 'English (Palau)',
                    'value' => 'en_PW'
                ],
                [
                    'key' => 'English (Papua New Guinea)',
                    'value' => 'en_PG'
                ],
                [
                    'key' => 'English (Philippines)',
                    'value' => 'en_PH'
                ],
                [
                    'key' => 'English (Pitcairn Islands)',
                    'value' => 'en_PN'
                ],
                [
                    'key' => 'English (Puerto Rico)',
                    'value' => 'en_PR'
                ],
                [
                    'key' => 'English (Rwanda)',
                    'value' => 'en_RW'
                ],
                [
                    'key' => 'English (Samoa)',
                    'value' => 'en_WS'
                ],
                [
                    'key' => 'English (Seychelles)',
                    'value' => 'en_SC'
                ],
                [
                    'key' => 'English (Sierra Leone)',
                    'value' => 'en_SL'
                ],
                [
                    'key' => 'English (Singapore)',
                    'value' => 'en_SG'
                ],
                [
                    'key' => 'English (Sint Maarten)',
                    'value' => 'en_SX'
                ],
                [
                    'key' => 'English (Slovenia)',
                    'value' => 'en_SI'
                ],
                [
                    'key' => 'English (Solomon Islands)',
                    'value' => 'en_SB'
                ],
                [
                    'key' => 'English (South Africa)',
                    'value' => 'en_ZA'
                ],
                [
                    'key' => 'English (South Sudan)',
                    'value' => 'en_SS'
                ],
                [
                    'key' => 'English (St. Helena)',
                    'value' => 'en_SH'
                ],
                [
                    'key' => 'English (St. Kitts & Nevis)',
                    'value' => 'en_KN'
                ],
                [
                    'key' => 'English (St. Lucia)',
                    'value' => 'en_LC'
                ],
                [
                    'key' => 'English (St. Vincent & Grenadines)',
                    'value' => 'en_VC'
                ],
                [
                    'key' => 'English (Sudan)',
                    'value' => 'en_SD'
                ],
                [
                    'key' => 'English (Swaziland)',
                    'value' => 'en_SZ'
                ],
                [
                    'key' => 'English (Sweden)',
                    'value' => 'en_SE'
                ],
                [
                    'key' => 'English (Switzerland)',
                    'value' => 'en_CH'
                ],
                [
                    'key' => 'English (Tanzania)',
                    'value' => 'en_TZ'
                ],
                [
                    'key' => 'English (Tokelau)',
                    'value' => 'en_TK'
                ],
                [
                    'key' => 'English (Tonga)',
                    'value' => 'en_TO'
                ],
                [
                    'key' => 'English (Trinidad & Tobago)',
                    'value' => 'en_TT'
                ],
                [
                    'key' => 'English (Turks & Caicos Islands)',
                    'value' => 'en_TC'
                ],
                [
                    'key' => 'English (Tuvalu)',
                    'value' => 'en_TV'
                ],
                [
                    'key' => 'English (U.S. Outlying Islands)',
                    'value' => 'en_UM'
                ],
                [
                    'key' => 'English (U.S. Virgin Islands)',
                    'value' => 'en_VI'
                ],
                [
                    'key' => 'English (Uganda)',
                    'value' => 'en_UG'
                ],
                [
                    'key' => 'English (United Kingdom)',
                    'value' => 'en_GB'
                ],
                [
                    'key' => 'English (United States)',
                    'value' => 'en_US_POSIX'
                ],
                [
                    'key' => 'English (United States)',
                    'value' => 'en_US'
                ],
                [
                    'key' => 'English (Vanuatu)',
                    'value' => 'en_VU'
                ],
                [
                    'key' => 'English (World)',
                    'value' => 'en_001'
                ],
                [
                    'key' => 'English (Zambia)',
                    'value' => 'en_ZM'
                ],
                [
                    'key' => 'English (Zimbabwe)',
                    'value' => 'en_ZW'
                ],
                [
                    'key' => 'Esperanto',
                    'value' => 'eo'
                ],
                [
                    'key' => 'Estonian',
                    'value' => 'et'
                ],
                [
                    'key' => 'Estonian (Estonia)',
                    'value' => 'et_EE'
                ],
                [
                    'key' => 'Ewe',
                    'value' => 'ee'
                ],
                [
                    'key' => 'Ewe (Ghana)',
                    'value' => 'ee_GH'
                ],
                [
                    'key' => 'Ewe (Togo)',
                    'value' => 'ee_TG'
                ],
                [
                    'key' => 'Ewondo',
                    'value' => 'ewo'
                ],
                [
                    'key' => 'Ewondo (Cameroon)',
                    'value' => 'ewo_CM'
                ],
                [
                    'key' => 'Faroese',
                    'value' => 'fo'
                ],
                [
                    'key' => 'Faroese (Denmark)',
                    'value' => 'fo_DK'
                ],
                [
                    'key' => 'Faroese (Faroe Islands)',
                    'value' => 'fo_FO'
                ],
                [
                    'key' => 'Filipino',
                    'value' => 'fil'
                ],
                [
                    'key' => 'Filipino (Philippines)',
                    'value' => 'fil_PH'
                ],
                [
                    'key' => 'Finnish',
                    'value' => 'fi'
                ],
                [
                    'key' => 'Finnish (Finland)',
                    'value' => 'fi_FI'
                ],
                [
                    'key' => 'French',
                    'value' => 'fr'
                ],
                [
                    'key' => 'French (Algeria)',
                    'value' => 'fr_DZ'
                ],
                [
                    'key' => 'French (Belgium)',
                    'value' => 'fr_BE'
                ],
                [
                    'key' => 'French (Benin)',
                    'value' => 'fr_BJ'
                ],
                [
                    'key' => 'French (Burkina Faso)',
                    'value' => 'fr_BF'
                ],
                [
                    'key' => 'French (Burundi)',
                    'value' => 'fr_BI'
                ],
                [
                    'key' => 'French (Cameroon)',
                    'value' => 'fr_CM'
                ],
                [
                    'key' => 'French (Canada)',
                    'value' => 'fr_CA'
                ],
                [
                    'key' => 'French (Central African Republic)',
                    'value' => 'fr_CF'
                ],
                [
                    'key' => 'French (Chad)',
                    'value' => 'fr_TD'
                ],
                [
                    'key' => 'French (Comoros)',
                    'value' => 'fr_KM'
                ],
                [
                    'key' => 'French (Congo - Brazzaville)',
                    'value' => 'fr_CG'
                ],
                [
                    'key' => 'French (Congo - Kinshasa)',
                    'value' => 'fr_CD'
                ],
                [
                    'key' => 'French (C\u00f4te d\u2019Ivoire)',
                    'value' => 'fr_CI'
                ],
                [
                    'key' => 'French (Djibouti)',
                    'value' => 'fr_DJ'
                ],
                [
                    'key' => 'French (Equatorial Guinea)',
                    'value' => 'fr_GQ'
                ],
                [
                    'key' => 'French (France)',
                    'value' => 'fr_FR'
                ],
                [
                    'key' => 'French (French Guiana)',
                    'value' => 'fr_GF'
                ],
                [
                    'key' => 'French (French Polynesia)',
                    'value' => 'fr_PF'
                ],
                [
                    'key' => 'French (Gabon)',
                    'value' => 'fr_GA'
                ],
                [
                    'key' => 'French (Guadeloupe)',
                    'value' => 'fr_GP'
                ],
                [
                    'key' => 'French (Guinea)',
                    'value' => 'fr_GN'
                ],
                [
                    'key' => 'French (Haiti)',
                    'value' => 'fr_HT'
                ],
                [
                    'key' => 'French (Luxembourg)',
                    'value' => 'fr_LU'
                ],
                [
                    'key' => 'French (Madagascar)',
                    'value' => 'fr_MG'
                ],
                [
                    'key' => 'French (Mali)',
                    'value' => 'fr_ML'
                ],
                [
                    'key' => 'French (Martinique)',
                    'value' => 'fr_MQ'
                ],
                [
                    'key' => 'French (Mauritania)',
                    'value' => 'fr_MR'
                ],
                [
                    'key' => 'French (Mauritius)',
                    'value' => 'fr_MU'
                ],
                [
                    'key' => 'French (Mayotte)',
                    'value' => 'fr_YT'
                ],
                [
                    'key' => 'French (Monaco)',
                    'value' => 'fr_MC'
                ],
                [
                    'key' => 'French (Morocco)',
                    'value' => 'fr_MA'
                ],
                [
                    'key' => 'French (New Caledonia)',
                    'value' => 'fr_NC'
                ],
                [
                    'key' => 'French (Niger)',
                    'value' => 'fr_NE'
                ],
                [
                    'key' => 'French (Rwanda)',
                    'value' => 'fr_RW'
                ],
                [
                    'key' => 'French (R\u00e9union)',
                    'value' => 'fr_RE'
                ],
                [
                    'key' => 'French (Senegal)',
                    'value' => 'fr_SN'
                ],
                [
                    'key' => 'French (Seychelles)',
                    'value' => 'fr_SC'
                ],
                [
                    'key' => 'French (St. Barth\u00e9lemy)',
                    'value' => 'fr_BL'
                ],
                [
                    'key' => 'French (St. Martin)',
                    'value' => 'fr_MF'
                ],
                [
                    'key' => 'French (St. Pierre & Miquelon)',
                    'value' => 'fr_PM'
                ],
                [
                    'key' => 'French (Switzerland)',
                    'value' => 'fr_CH'
                ],
                [
                    'key' => 'French (Syria)',
                    'value' => 'fr_SY'
                ],
                [
                    'key' => 'French (Togo)',
                    'value' => 'fr_TG'
                ],
                [
                    'key' => 'French (Tunisia)',
                    'value' => 'fr_TN'
                ],
                [
                    'key' => 'French (Vanuatu)',
                    'value' => 'fr_VU'
                ],
                [
                    'key' => 'French (Wallis & Futuna)',
                    'value' => 'fr_WF'
                ],
                [
                    'key' => 'Friulian',
                    'value' => 'fur'
                ],
                [
                    'key' => 'Friulian (Italy)',
                    'value' => 'fur_IT'
                ],
                [
                    'key' => 'Fulah',
                    'value' => 'ff'
                ],
                [
                    'key' => 'Fulah (Cameroon)',
                    'value' => 'ff_CM'
                ],
                [
                    'key' => 'Fulah (Guinea)',
                    'value' => 'ff_GN'
                ],
                [
                    'key' => 'Fulah (Mauritania)',
                    'value' => 'ff_MR'
                ],
                [
                    'key' => 'Fulah (Senegal)',
                    'value' => 'ff_SN'
                ],
                [
                    'key' => 'Galician',
                    'value' => 'gl'
                ],
                [
                    'key' => 'Galician (Spain)',
                    'value' => 'gl_ES'
                ],
                [
                    'key' => 'Ganda',
                    'value' => 'lg'
                ],
                [
                    'key' => 'Ganda (Uganda)',
                    'value' => 'lg_UG'
                ],
                [
                    'key' => 'Georgian',
                    'value' => 'ka'
                ],
                [
                    'key' => 'Georgian (Georgia)',
                    'value' => 'ka_GE'
                ],
                [
                    'key' => 'German',
                    'value' => 'de'
                ],
                [
                    'key' => 'German (Austria)',
                    'value' => 'de_AT'
                ],
                [
                    'key' => 'German (Belgium)',
                    'value' => 'de_BE'
                ],
                [
                    'key' => 'German (Germany)',
                    'value' => 'de_DE'
                ],
                [
                    'key' => 'German (Liechtenstein)',
                    'value' => 'de_LI'
                ],
                [
                    'key' => 'German (Luxembourg)',
                    'value' => 'de_LU'
                ],
                [
                    'key' => 'German (Switzerland)',
                    'value' => 'de_CH'
                ],
                [
                    'key' => 'Greek',
                    'value' => 'el'
                ],
                [
                    'key' => 'Greek (Cyprus)',
                    'value' => 'el_CY'
                ],
                [
                    'key' => 'Greek (Greece)',
                    'value' => 'el_GR'
                ],
                [
                    'key' => 'Gujarati',
                    'value' => 'gu'
                ],
                [
                    'key' => 'Gujarati (India)',
                    'value' => 'gu_IN'
                ],
                [
                    'key' => 'Gusii',
                    'value' => 'guz'
                ],
                [
                    'key' => 'Gusii (Kenya)',
                    'value' => 'guz_KE'
                ],
                [
                    'key' => 'Hausa',
                    'value' => 'ha'
                ],
                [
                    'key' => 'Hausa (Ghana)',
                    'value' => 'ha_GH'
                ],
                [
                    'key' => 'Hausa (Niger)',
                    'value' => 'ha_NE'
                ],
                [
                    'key' => 'Hausa (Nigeria)',
                    'value' => 'ha_NG'
                ],
                [
                    'key' => 'Hawaiian',
                    'value' => 'haw'
                ],
                [
                    'key' => 'Hawaiian (United States)',
                    'value' => 'haw_US'
                ],
                [
                    'key' => 'Hebrew',
                    'value' => 'he'
                ],
                [
                    'key' => 'Hebrew (Israel)',
                    'value' => 'he_IL'
                ],
                [
                    'key' => 'Hindi',
                    'value' => 'hi'
                ],
                [
                    'key' => 'Hindi (India)',
                    'value' => 'hi_IN'
                ],
                [
                    'key' => 'Hungarian',
                    'value' => 'hu'
                ],
                [
                    'key' => 'Hungarian (Hungary)',
                    'value' => 'hu_HU'
                ],
                [
                    'key' => 'Icelandic',
                    'value' => 'is'
                ],
                [
                    'key' => 'Icelandic (Iceland)',
                    'value' => 'is_IS'
                ],
                [
                    'key' => 'Igbo',
                    'value' => 'ig'
                ],
                [
                    'key' => 'Igbo (Nigeria)',
                    'value' => 'ig_NG'
                ],
                [
                    'key' => 'Inari Sami',
                    'value' => 'smn'
                ],
                [
                    'key' => 'Inari Sami (Finland)',
                    'value' => 'smn_FI'
                ],
                [
                    'key' => 'Indonesian',
                    'value' => 'id'
                ],
                [
                    'key' => 'Indonesian (Indonesia)',
                    'value' => 'id_ID'
                ],
                [
                    'key' => 'Irish',
                    'value' => 'ga'
                ],
                [
                    'key' => 'Irish (Ireland)',
                    'value' => 'ga_IE'
                ],
                [
                    'key' => 'Italian',
                    'value' => 'it'
                ],
                [
                    'key' => 'Italian (Italy)',
                    'value' => 'it_IT'
                ],
                [
                    'key' => 'Italian (San Marino)',
                    'value' => 'it_SM'
                ],
                [
                    'key' => 'Italian (Switzerland)',
                    'value' => 'it_CH'
                ],
                [
                    'key' => 'Japanese',
                    'value' => 'ja'
                ],
                [
                    'key' => 'Japanese (Japan)',
                    'value' => 'ja_JP'
                ],
                [
                    'key' => 'Jola-Fonyi',
                    'value' => 'dyo'
                ],
                [
                    'key' => 'Jola-Fonyi (Senegal)',
                    'value' => 'dyo_SN'
                ],
                [
                    'key' => 'Kabuverdianu',
                    'value' => 'kea'
                ],
                [
                    'key' => 'Kabuverdianu (Cape Verde)',
                    'value' => 'kea_CV'
                ],
                [
                    'key' => 'Kabyle',
                    'value' => 'kab'
                ],
                [
                    'key' => 'Kabyle (Algeria)',
                    'value' => 'kab_DZ'
                ],
                [
                    'key' => 'Kako',
                    'value' => 'kkj'
                ],
                [
                    'key' => 'Kako (Cameroon)',
                    'value' => 'kkj_CM'
                ],
                [
                    'key' => 'Kalaallisut',
                    'value' => 'kl'
                ],
                [
                    'key' => 'Kalaallisut (Greenland)',
                    'value' => 'kl_GL'
                ],
                [
                    'key' => 'Kalenjin',
                    'value' => 'kln'
                ],
                [
                    'key' => 'Kalenjin (Kenya)',
                    'value' => 'kln_KE'
                ],
                [
                    'key' => 'Kamba',
                    'value' => 'kam'
                ],
                [
                    'key' => 'Kamba (Kenya)',
                    'value' => 'kam_KE'
                ],
                [
                    'key' => 'Kannada',
                    'value' => 'kn'
                ],
                [
                    'key' => 'Kannada (India)',
                    'value' => 'kn_IN'
                ],
                [
                    'key' => 'Kashmiri',
                    'value' => 'ks'
                ],
                [
                    'key' => 'Kashmiri (India)',
                    'value' => 'ks_IN'
                ],
                [
                    'key' => 'Kazakh',
                    'value' => 'kk'
                ],
                [
                    'key' => 'Kazakh (Kazakhstan)',
                    'value' => 'kk_KZ'
                ],
                [
                    'key' => 'Khmer',
                    'value' => 'km'
                ],
                [
                    'key' => 'Khmer (Cambodia)',
                    'value' => 'km_KH'
                ],
                [
                    'key' => 'Kikuyu',
                    'value' => 'ki'
                ],
                [
                    'key' => 'Kikuyu (Kenya)',
                    'value' => 'ki_KE'
                ],
                [
                    'key' => 'Kinyarwanda',
                    'value' => 'rw'
                ],
                [
                    'key' => 'Kinyarwanda (Rwanda)',
                    'value' => 'rw_RW'
                ],
                [
                    'key' => 'Konkani',
                    'value' => 'kok'
                ],
                [
                    'key' => 'Konkani (India)',
                    'value' => 'kok_IN'
                ],
                [
                    'key' => 'Korean',
                    'value' => 'ko'
                ],
                [
                    'key' => 'Korean (North Korea)',
                    'value' => 'ko_KP'
                ],
                [
                    'key' => 'Korean (South Korea)',
                    'value' => 'ko_KR'
                ],
                [
                    'key' => 'Koyra Chiini',
                    'value' => 'khq'
                ],
                [
                    'key' => 'Koyra Chiini (Mali)',
                    'value' => 'khq_ML'
                ],
                [
                    'key' => 'Koyraboro Senni',
                    'value' => 'ses'
                ],
                [
                    'key' => 'Koyraboro Senni (Mali)',
                    'value' => 'ses_ML'
                ],
                [
                    'key' => 'Kwasio',
                    'value' => 'nmg'
                ],
                [
                    'key' => 'Kwasio (Cameroon)',
                    'value' => 'nmg_CM'
                ],
                [
                    'key' => 'Kyrgyz',
                    'value' => 'ky'
                ],
                [
                    'key' => 'Kyrgyz (Kyrgyzstan)',
                    'value' => 'ky_KG'
                ],
                [
                    'key' => 'Lakota',
                    'value' => 'lkt'
                ],
                [
                    'key' => 'Lakota (United States)',
                    'value' => 'lkt_US'
                ],
                [
                    'key' => 'Langi',
                    'value' => 'lag'
                ],
                [
                    'key' => 'Langi (Tanzania)',
                    'value' => 'lag_TZ'
                ],
                [
                    'key' => 'Lao',
                    'value' => 'lo'
                ],
                [
                    'key' => 'Lao (Laos)',
                    'value' => 'lo_LA'
                ],
                [
                    'key' => 'Latvian',
                    'value' => 'lv'
                ],
                [
                    'key' => 'Latvian (Latvia)',
                    'value' => 'lv_LV'
                ],
                [
                    'key' => 'Lingala',
                    'value' => 'ln'
                ],
                [
                    'key' => 'Lingala (Angola)',
                    'value' => 'ln_AO'
                ],
                [
                    'key' => 'Lingala (Central African Republic)',
                    'value' => 'ln_CF'
                ],
                [
                    'key' => 'Lingala (Congo - Brazzaville)',
                    'value' => 'ln_CG'
                ],
                [
                    'key' => 'Lingala (Congo - Kinshasa)',
                    'value' => 'ln_CD'
                ],
                [
                    'key' => 'Lithuanian',
                    'value' => 'lt'
                ],
                [
                    'key' => 'Lithuanian (Lithuania)',
                    'value' => 'lt_LT'
                ],
                [
                    'key' => 'Lower Sorbian',
                    'value' => 'dsb'
                ],
                [
                    'key' => 'Lower Sorbian (Germany)',
                    'value' => 'dsb_DE'
                ],
                [
                    'key' => 'Luba-Katanga',
                    'value' => 'lu'
                ],
                [
                    'key' => 'Luba-Katanga (Congo - Kinshasa)',
                    'value' => 'lu_CD'
                ],
                [
                    'key' => 'Luo',
                    'value' => 'luo'
                ],
                [
                    'key' => 'Luo (Kenya)',
                    'value' => 'luo_KE'
                ],
                [
                    'key' => 'Luxembourgish',
                    'value' => 'lb'
                ],
                [
                    'key' => 'Luxembourgish (Luxembourg)',
                    'value' => 'lb_LU'
                ],
                [
                    'key' => 'Luyia',
                    'value' => 'luy'
                ],
                [
                    'key' => 'Luyia (Kenya)',
                    'value' => 'luy_KE'
                ],
                [
                    'key' => 'Macedonian',
                    'value' => 'mk'
                ],
                [
                    'key' => 'Macedonian (Macedonia)',
                    'value' => 'mk_MK'
                ],
                [
                    'key' => 'Machame',
                    'value' => 'jmc'
                ],
                [
                    'key' => 'Machame (Tanzania)',
                    'value' => 'jmc_TZ'
                ],
                [
                    'key' => 'Makhuwa-Meetto',
                    'value' => 'mgh'
                ],
                [
                    'key' => 'Makhuwa-Meetto (Mozambique)',
                    'value' => 'mgh_MZ'
                ],
                [
                    'key' => 'Makonde',
                    'value' => 'kde'
                ],
                [
                    'key' => 'Makonde (Tanzania)',
                    'value' => 'kde_TZ'
                ],
                [
                    'key' => 'Malagasy',
                    'value' => 'mg'
                ],
                [
                    'key' => 'Malagasy (Madagascar)',
                    'value' => 'mg_MG'
                ],
                [
                    'key' => 'Malay',
                    'value' => 'ms'
                ],
                [
                    'key' => 'Malay (Brunei)',
                    'value' => 'ms_BN'
                ],
                [
                    'key' => 'Malay (Malaysia)',
                    'value' => 'ms_MY'
                ],
                [
                    'key' => 'Malay (Singapore)',
                    'value' => 'ms_SG'
                ],
                [
                    'key' => 'Malayalam',
                    'value' => 'ml'
                ],
                [
                    'key' => 'Malayalam (India)',
                    'value' => 'ml_IN'
                ],
                [
                    'key' => 'Maltese',
                    'value' => 'mt'
                ],
                [
                    'key' => 'Maltese (Malta)',
                    'value' => 'mt_MT'
                ],
                [
                    'key' => 'Manx',
                    'value' => 'gv'
                ],
                [
                    'key' => 'Manx (Isle of Man)',
                    'value' => 'gv_IM'
                ],
                [
                    'key' => 'Marathi',
                    'value' => 'mr'
                ],
                [
                    'key' => 'Marathi (India)',
                    'value' => 'mr_IN'
                ],
                [
                    'key' => 'Masai',
                    'value' => 'mas'
                ],
                [
                    'key' => 'Masai (Kenya)',
                    'value' => 'mas_KE'
                ],
                [
                    'key' => 'Masai (Tanzania)',
                    'value' => 'mas_TZ'
                ],
                [
                    'key' => 'Mazanderani',
                    'value' => 'mzn'
                ],
                [
                    'key' => 'Mazanderani (Iran)',
                    'value' => 'mzn_IR'
                ],
                [
                    'key' => 'Meru',
                    'value' => 'mer'
                ],
                [
                    'key' => 'Meru (Kenya)',
                    'value' => 'mer_KE'
                ],
                [
                    'key' => 'Meta\u02bc',
                    'value' => 'mgo'
                ],
                [
                    'key' => 'Meta\u02bc (Cameroon)',
                    'value' => 'mgo_CM'
                ],
                [
                    'key' => 'Mongolian',
                    'value' => 'mn'
                ],
                [
                    'key' => 'Mongolian (Mongolia)',
                    'value' => 'mn_MN'
                ],
                [
                    'key' => 'Morisyen',
                    'value' => 'mfe'
                ],
                [
                    'key' => 'Morisyen (Mauritius)',
                    'value' => 'mfe_MU'
                ],
                [
                    'key' => 'Mundang',
                    'value' => 'mua'
                ],
                [
                    'key' => 'Mundang (Cameroon)',
                    'value' => 'mua_CM'
                ],
                [
                    'key' => 'Nama',
                    'value' => 'naq'
                ],
                [
                    'key' => 'Nama (Namibia)',
                    'value' => 'naq_NA'
                ],
                [
                    'key' => 'Nepali',
                    'value' => 'ne'
                ],
                [
                    'key' => 'Nepali (India)',
                    'value' => 'ne_IN'
                ],
                [
                    'key' => 'Nepali (Nepal)',
                    'value' => 'ne_NP'
                ],
                [
                    'key' => 'Ngiemboon',
                    'value' => 'nnh'
                ],
                [
                    'key' => 'Ngiemboon (Cameroon)',
                    'value' => 'nnh_CM'
                ],
                [
                    'key' => 'Ngomba',
                    'value' => 'jgo'
                ],
                [
                    'key' => 'Ngomba (Cameroon)',
                    'value' => 'jgo_CM'
                ],
                [
                    'key' => 'North Ndebele',
                    'value' => 'nd'
                ],
                [
                    'key' => 'North Ndebele (Zimbabwe)',
                    'value' => 'nd_ZW'
                ],
                [
                    'key' => 'Northern Luri',
                    'value' => 'lrc'
                ],
                [
                    'key' => 'Northern Luri (Iran)',
                    'value' => 'lrc_IR'
                ],
                [
                    'key' => 'Northern Luri (Iraq)',
                    'value' => 'lrc_IQ'
                ],
                [
                    'key' => 'Northern Sami',
                    'value' => 'se'
                ],
                [
                    'key' => 'Northern Sami (Finland)',
                    'value' => 'se_FI'
                ],
                [
                    'key' => 'Northern Sami (Norway)',
                    'value' => 'se_NO'
                ],
                [
                    'key' => 'Northern Sami (Sweden)',
                    'value' => 'se_SE'
                ],
                [
                    'key' => 'Norwegian Bokm\u00e5l',
                    'value' => 'nb'
                ],
                [
                    'key' => 'Norwegian Bokm\u00e5l (Norway)',
                    'value' => 'nb_NO'
                ],
                [
                    'key' => 'Norwegian Bokm\u00e5l (Svalbard & Jan Mayen)',
                    'value' => 'nb_SJ'
                ],
                [
                    'key' => 'Norwegian Nynorsk',
                    'value' => 'nn'
                ],
                [
                    'key' => 'Norwegian Nynorsk (Norway)',
                    'value' => 'nn_NO'
                ],
                [
                    'key' => 'Nuer',
                    'value' => 'nus'
                ],
                [
                    'key' => 'Nuer (South Sudan)',
                    'value' => 'nus_SS'
                ],
                [
                    'key' => 'Nyankole',
                    'value' => 'nyn'
                ],
                [
                    'key' => 'Nyankole (Uganda)',
                    'value' => 'nyn_UG'
                ],
                [
                    'key' => 'Oriya',
                    'value' => 'or'
                ],
                [
                    'key' => 'Oriya (India)',
                    'value' => 'or_IN'
                ],
                [
                    'key' => 'Oromo',
                    'value' => 'om'
                ],
                [
                    'key' => 'Oromo (Ethiopia)',
                    'value' => 'om_ET'
                ],
                [
                    'key' => 'Oromo (Kenya)',
                    'value' => 'om_KE'
                ],
                [
                    'key' => 'Ossetic',
                    'value' => 'os'
                ],
                [
                    'key' => 'Ossetic (Georgia)',
                    'value' => 'os_GE'
                ],
                [
                    'key' => 'Ossetic (Russia)',
                    'value' => 'os_RU'
                ],
                [
                    'key' => 'Pashto',
                    'value' => 'ps'
                ],
                [
                    'key' => 'Pashto (Afghanistan)',
                    'value' => 'ps_AF'
                ],
                [
                    'key' => 'Persian',
                    'value' => 'fa'
                ],
                [
                    'key' => 'Persian (Afghanistan)',
                    'value' => 'fa_AF'
                ],
                [
                    'key' => 'Persian (Iran)',
                    'value' => 'fa_IR'
                ],
                [
                    'key' => 'Polish',
                    'value' => 'pl'
                ],
                [
                    'key' => 'Polish (Poland)',
                    'value' => 'pl_PL'
                ],
                [
                    'key' => 'Portuguese',
                    'value' => 'pt'
                ],
                [
                    'key' => 'Portuguese (Angola)',
                    'value' => 'pt_AO'
                ],
                [
                    'key' => 'Portuguese (Brazil)',
                    'value' => 'pt_BR'
                ],
                [
                    'key' => 'Portuguese (Cape Verde)',
                    'value' => 'pt_CV'
                ],
                [
                    'key' => 'Portuguese (Guinea-Bissau)',
                    'value' => 'pt_GW'
                ],
                [
                    'key' => 'Portuguese (Macau SAR China)',
                    'value' => 'pt_MO'
                ],
                [
                    'key' => 'Portuguese (Mozambique)',
                    'value' => 'pt_MZ'
                ],
                [
                    'key' => 'Portuguese (Portugal)',
                    'value' => 'pt_PT'
                ],
                [
                    'key' => 'Portuguese (S\u00e3o Tom\u00e9 & Pr\u00edncipe)',
                    'value' => 'pt_ST'
                ],
                [
                    'key' => 'Portuguese (Timor-Leste)',
                    'value' => 'pt_TL'
                ],
                [
                    'key' => 'Punjabi',
                    'value' => 'pa'
                ],
                [
                    'key' => 'Punjabi',
                    'value' => 'pa_Guru'
                ],
                [
                    'key' => 'Punjabi',
                    'value' => 'pa_Arab'
                ],
                [
                    'key' => 'Punjabi (India)',
                    'value' => 'pa_Guru_IN'
                ],
                [
                    'key' => 'Punjabi (Pakistan)',
                    'value' => 'pa_Arab_PK'
                ],
                [
                    'key' => 'Quechua',
                    'value' => 'qu'
                ],
                [
                    'key' => 'Quechua (Bolivia)',
                    'value' => 'qu_BO'
                ],
                [
                    'key' => 'Quechua (Ecuador)',
                    'value' => 'qu_EC'
                ],
                [
                    'key' => 'Quechua (Peru)',
                    'value' => 'qu_PE'
                ],
                [
                    'key' => 'Romanian',
                    'value' => 'ro'
                ],
                [
                    'key' => 'Romanian (Moldova)',
                    'value' => 'ro_MD'
                ],
                [
                    'key' => 'Romanian (Romania)',
                    'value' => 'ro_RO'
                ],
                [
                    'key' => 'Romansh',
                    'value' => 'rm'
                ],
                [
                    'key' => 'Romansh (Switzerland)',
                    'value' => 'rm_CH'
                ],
                [
                    'key' => 'Rombo',
                    'value' => 'rof'
                ],
                [
                    'key' => 'Rombo (Tanzania)',
                    'value' => 'rof_TZ'
                ],
                [
                    'key' => 'Rundi',
                    'value' => 'rn'
                ],
                [
                    'key' => 'Rundi (Burundi)',
                    'value' => 'rn_BI'
                ],
                [
                    'key' => 'Russian',
                    'value' => 'ru'
                ],
                [
                    'key' => 'Russian (Belarus)',
                    'value' => 'ru_BY'
                ],
                [
                    'key' => 'Russian (Kazakhstan)',
                    'value' => 'ru_KZ'
                ],
                [
                    'key' => 'Russian (Kyrgyzstan)',
                    'value' => 'ru_KG'
                ],
                [
                    'key' => 'Russian (Moldova)',
                    'value' => 'ru_MD'
                ],
                [
                    'key' => 'Russian (Russia)',
                    'value' => 'ru_RU'
                ],
                [
                    'key' => 'Russian (Ukraine)',
                    'value' => 'ru_UA'
                ],
                [
                    'key' => 'Rwa',
                    'value' => 'rwk'
                ],
                [
                    'key' => 'Rwa (Tanzania)',
                    'value' => 'rwk_TZ'
                ],
                [
                    'key' => 'Sakha',
                    'value' => 'sah'
                ],
                [
                    'key' => 'Sakha (Russia)',
                    'value' => 'sah_RU'
                ],
                [
                    'key' => 'Samburu',
                    'value' => 'saq'
                ],
                [
                    'key' => 'Samburu (Kenya)',
                    'value' => 'saq_KE'
                ],
                [
                    'key' => 'Sango',
                    'value' => 'sg'
                ],
                [
                    'key' => 'Sango (Central African Republic)',
                    'value' => 'sg_CF'
                ],
                [
                    'key' => 'Sangu',
                    'value' => 'sbp'
                ],
                [
                    'key' => 'Sangu (Tanzania)',
                    'value' => 'sbp_TZ'
                ],
                [
                    'key' => 'Scottish Gaelic',
                    'value' => 'gd'
                ],
                [
                    'key' => 'Scottish Gaelic (United Kingdom)',
                    'value' => 'gd_GB'
                ],
                [
                    'key' => 'Sena',
                    'value' => 'seh'
                ],
                [
                    'key' => 'Sena (Mozambique)',
                    'value' => 'seh_MZ'
                ],
                [
                    'key' => 'Serbian',
                    'value' => 'sr_Cyrl'
                ],
                [
                    'key' => 'Serbian',
                    'value' => 'sr_Latn'
                ],
                [
                    'key' => 'Serbian',
                    'value' => 'sr'
                ],
                [
                    'key' => 'Serbian (Bosnia & Herzegovina)',
                    'value' => 'sr_Latn_BA'
                ],
                [
                    'key' => 'Serbian (Bosnia & Herzegovina)',
                    'value' => 'sr_Cyrl_BA'
                ],
                [
                    'key' => 'Serbian (Kosovo)',
                    'value' => 'sr_Cyrl_XK'
                ],
                [
                    'key' => 'Serbian (Kosovo)',
                    'value' => 'sr_Latn_XK'
                ],
                [
                    'key' => 'Serbian (Montenegro)',
                    'value' => 'sr_Cyrl_ME'
                ],
                [
                    'key' => 'Serbian (Montenegro)',
                    'value' => 'sr_Latn_ME'
                ],
                [
                    'key' => 'Serbian (Serbia)',
                    'value' => 'sr_Cyrl_RS'
                ],
                [
                    'key' => 'Serbian (Serbia)',
                    'value' => 'sr_Latn_RS'
                ],
                [
                    'key' => 'Shambala',
                    'value' => 'ksb'
                ],
                [
                    'key' => 'Shambala (Tanzania)',
                    'value' => 'ksb_TZ'
                ],
                [
                    'key' => 'Shona',
                    'value' => 'sn'
                ],
                [
                    'key' => 'Shona (Zimbabwe)',
                    'value' => 'sn_ZW'
                ],
                [
                    'key' => 'Sichuan Yi',
                    'value' => 'ii'
                ],
                [
                    'key' => 'Sichuan Yi (China)',
                    'value' => 'ii_CN'
                ],
                [
                    'key' => 'Sinhala',
                    'value' => 'si'
                ],
                [
                    'key' => 'Sinhala (Sri Lanka)',
                    'value' => 'si_LK'
                ],
                [
                    'key' => 'Slovak',
                    'value' => 'sk'
                ],
                [
                    'key' => 'Slovak (Slovakia)',
                    'value' => 'sk_SK'
                ],
                [
                    'key' => 'Slovenian',
                    'value' => 'sl'
                ],
                [
                    'key' => 'Slovenian (Slovenia)',
                    'value' => 'sl_SI'
                ],
                [
                    'key' => 'Soga',
                    'value' => 'xog'
                ],
                [
                    'key' => 'Soga (Uganda)',
                    'value' => 'xog_UG'
                ],
                [
                    'key' => 'Somali',
                    'value' => 'so'
                ],
                [
                    'key' => 'Somali (Djibouti)',
                    'value' => 'so_DJ'
                ],
                [
                    'key' => 'Somali (Ethiopia)',
                    'value' => 'so_ET'
                ],
                [
                    'key' => 'Somali (Kenya)',
                    'value' => 'so_KE'
                ],
                [
                    'key' => 'Somali (Somalia)',
                    'value' => 'so_SO'
                ],
                [
                    'key' => 'Spanish',
                    'value' => 'es'
                ],
                [
                    'key' => 'Spanish (Argentina)',
                    'value' => 'es_AR'
                ],
                [
                    'key' => 'Spanish (Bolivia)',
                    'value' => 'es_BO'
                ],
                [
                    'key' => 'Spanish (Canary Islands)',
                    'value' => 'es_IC'
                ],
                [
                    'key' => 'Spanish (Ceuta & Melilla)',
                    'value' => 'es_EA'
                ],
                [
                    'key' => 'Spanish (Chile)',
                    'value' => 'es_CL'
                ],
                [
                    'key' => 'Spanish (Colombia)',
                    'value' => 'es_CO'
                ],
                [
                    'key' => 'Spanish (Costa Rica)',
                    'value' => 'es_CR'
                ],
                [
                    'key' => 'Spanish (Cuba)',
                    'value' => 'es_CU'
                ],
                [
                    'key' => 'Spanish (Dominican Republic)',
                    'value' => 'es_DO'
                ],
                [
                    'key' => 'Spanish (Ecuador)',
                    'value' => 'es_EC'
                ],
                [
                    'key' => 'Spanish (El Salvador)',
                    'value' => 'es_SV'
                ],
                [
                    'key' => 'Spanish (Equatorial Guinea)',
                    'value' => 'es_GQ'
                ],
                [
                    'key' => 'Spanish (Guatemala)',
                    'value' => 'es_GT'
                ],
                [
                    'key' => 'Spanish (Honduras)',
                    'value' => 'es_HN'
                ],
                [
                    'key' => 'Spanish (Latin America)',
                    'value' => 'es_419'
                ],
                [
                    'key' => 'Spanish (Mexico)',
                    'value' => 'es_MX'
                ],
                [
                    'key' => 'Spanish (Nicaragua)',
                    'value' => 'es_NI'
                ],
                [
                    'key' => 'Spanish (Panama)',
                    'value' => 'es_PA'
                ],
                [
                    'key' => 'Spanish (Paraguay)',
                    'value' => 'es_PY'
                ],
                [
                    'key' => 'Spanish (Peru)',
                    'value' => 'es_PE'
                ],
                [
                    'key' => 'Spanish (Philippines)',
                    'value' => 'es_PH'
                ],
                [
                    'key' => 'Spanish (Puerto Rico)',
                    'value' => 'es_PR'
                ],
                [
                    'key' => 'Spanish (Spain)',
                    'value' => 'es_ES'
                ],
                [
                    'key' => 'Spanish (United States)',
                    'value' => 'es_US'
                ],
                [
                    'key' => 'Spanish (Uruguay)',
                    'value' => 'es_UY'
                ],
                [
                    'key' => 'Spanish (Venezuela)',
                    'value' => 'es_VE'
                ],
                [
                    'key' => 'Standard Moroccan Tamazight',
                    'value' => 'zgh'
                ],
                [
                    'key' => 'Standard Moroccan Tamazight (Morocco)',
                    'value' => 'zgh_MA'
                ],
                [
                    'key' => 'Swahili',
                    'value' => 'sw'
                ],
                [
                    'key' => 'Swahili (Congo - Kinshasa)',
                    'value' => 'sw_CD'
                ],
                [
                    'key' => 'Swahili (Kenya)',
                    'value' => 'sw_KE'
                ],
                [
                    'key' => 'Swahili (Tanzania)',
                    'value' => 'sw_TZ'
                ],
                [
                    'key' => 'Swahili (Uganda)',
                    'value' => 'sw_UG'
                ],
                [
                    'key' => 'Swedish',
                    'value' => 'sv'
                ],
                [
                    'key' => 'Swedish (Finland)',
                    'value' => 'sv_FI'
                ],
                [
                    'key' => 'Swedish (Sweden)',
                    'value' => 'sv_SE'
                ],
                [
                    'key' => 'Swedish (\u00c5land Islands)',
                    'value' => 'sv_AX'
                ],
                [
                    'key' => 'Swiss German',
                    'value' => 'gsw'
                ],
                [
                    'key' => 'Swiss German (France)',
                    'value' => 'gsw_FR'
                ],
                [
                    'key' => 'Swiss German (Liechtenstein)',
                    'value' => 'gsw_LI'
                ],
                [
                    'key' => 'Swiss German (Switzerland)',
                    'value' => 'gsw_CH'
                ],
                [
                    'key' => 'Tachelhit',
                    'value' => 'shi_Tfng'
                ],
                [
                    'key' => 'Tachelhit',
                    'value' => 'shi'
                ],
                [
                    'key' => 'Tachelhit',
                    'value' => 'shi_Latn'
                ],
                [
                    'key' => 'Tachelhit (Morocco)',
                    'value' => 'shi_Tfng_MA'
                ],
                [
                    'key' => 'Tachelhit (Morocco)',
                    'value' => 'shi_Latn_MA'
                ],
                [
                    'key' => 'Taita',
                    'value' => 'dav'
                ],
                [
                    'key' => 'Taita (Kenya)',
                    'value' => 'dav_KE'
                ],
                [
                    'key' => 'Tamil',
                    'value' => 'ta'
                ],
                [
                    'key' => 'Tamil (India)',
                    'value' => 'ta_IN'
                ],
                [
                    'key' => 'Tamil (Malaysia)',
                    'value' => 'ta_MY'
                ],
                [
                    'key' => 'Tamil (Singapore)',
                    'value' => 'ta_SG'
                ],
                [
                    'key' => 'Tamil (Sri Lanka)',
                    'value' => 'ta_LK'
                ],
                [
                    'key' => 'Tasawaq',
                    'value' => 'twq'
                ],
                [
                    'key' => 'Tasawaq (Niger)',
                    'value' => 'twq_NE'
                ],
                [
                    'key' => 'Telugu',
                    'value' => 'te'
                ],
                [
                    'key' => 'Telugu (India)',
                    'value' => 'te_IN'
                ],
                [
                    'key' => 'Teso',
                    'value' => 'teo'
                ],
                [
                    'key' => 'Teso (Kenya)',
                    'value' => 'teo_KE'
                ],
                [
                    'key' => 'Teso (Uganda)',
                    'value' => 'teo_UG'
                ],
                [
                    'key' => 'Thai',
                    'value' => 'th'
                ],
                [
                    'key' => 'Thai (Thailand)',
                    'value' => 'th_TH'
                ],
                [
                    'key' => 'Tibetan',
                    'value' => 'bo'
                ],
                [
                    'key' => 'Tibetan (China)',
                    'value' => 'bo_CN'
                ],
                [
                    'key' => 'Tibetan (India)',
                    'value' => 'bo_IN'
                ],
                [
                    'key' => 'Tigrinya',
                    'value' => 'ti'
                ],
                [
                    'key' => 'Tigrinya (Eritrea)',
                    'value' => 'ti_ER'
                ],
                [
                    'key' => 'Tigrinya (Ethiopia)',
                    'value' => 'ti_ET'
                ],
                [
                    'key' => 'Tongan',
                    'value' => 'to'
                ],
                [
                    'key' => 'Tongan (Tonga)',
                    'value' => 'to_TO'
                ],
                [
                    'key' => 'Turkish',
                    'value' => 'tr'
                ],
                [
                    'key' => 'Turkish (Cyprus)',
                    'value' => 'tr_CY'
                ],
                [
                    'key' => 'Turkish (Turkey)',
                    'value' => 'tr_TR'
                ],
                [
                    'key' => 'Ukrainian',
                    'value' => 'uk'
                ],
                [
                    'key' => 'Ukrainian (Ukraine)',
                    'value' => 'uk_UA'
                ],
                [
                    'key' => 'Upper Sorbian',
                    'value' => 'hsb'
                ],
                [
                    'key' => 'Upper Sorbian (Germany)',
                    'value' => 'hsb_DE'
                ],
                [
                    'key' => 'Urdu',
                    'value' => 'ur'
                ],
                [
                    'key' => 'Urdu (India)',
                    'value' => 'ur_IN'
                ],
                [
                    'key' => 'Urdu (Pakistan)',
                    'value' => 'ur_PK'
                ],
                [
                    'key' => 'Uyghur',
                    'value' => 'ug'
                ],
                [
                    'key' => 'Uyghur (China)',
                    'value' => 'ug_CN'
                ],
                [
                    'key' => 'Uzbek',
                    'value' => 'uz_Cyrl'
                ],
                [
                    'key' => 'Uzbek',
                    'value' => 'uz'
                ],
                [
                    'key' => 'Uzbek',
                    'value' => 'uz_Arab'
                ],
                [
                    'key' => 'Uzbek',
                    'value' => 'uz_Latn'
                ],
                [
                    'key' => 'Uzbek (Afghanistan)',
                    'value' => 'uz_Arab_AF'
                ],
                [
                    'key' => 'Uzbek (Uzbekistan)',
                    'value' => 'uz_Latn_UZ'
                ],
                [
                    'key' => 'Uzbek (Uzbekistan)',
                    'value' => 'uz_Cyrl_UZ'
                ],
                [
                    'key' => 'Vai',
                    'value' => 'vai_Vaii'
                ],
                [
                    'key' => 'Vai',
                    'value' => 'vai'
                ],
                [
                    'key' => 'Vai',
                    'value' => 'vai_Latn'
                ],
                [
                    'key' => 'Vai (Liberia)',
                    'value' => 'vai_Latn_LR'
                ],
                [
                    'key' => 'Vai (Liberia)',
                    'value' => 'vai_Vaii_LR'
                ],
                [
                    'key' => 'Vietnamese',
                    'value' => 'vi'
                ],
                [
                    'key' => 'Vietnamese (Vietnam)',
                    'value' => 'vi_VN'
                ],
                [
                    'key' => 'Vunjo',
                    'value' => 'vun'
                ],
                [
                    'key' => 'Vunjo (Tanzania)',
                    'value' => 'vun_TZ'
                ],
                [
                    'key' => 'Walser',
                    'value' => 'wae'
                ],
                [
                    'key' => 'Walser (Switzerland)',
                    'value' => 'wae_CH'
                ],
                [
                    'key' => 'Welsh',
                    'value' => 'cy'
                ],
                [
                    'key' => 'Welsh (United Kingdom)',
                    'value' => 'cy_GB'
                ],
                [
                    'key' => 'Western Frisian',
                    'value' => 'fy'
                ],
                [
                    'key' => 'Western Frisian (Netherlands)',
                    'value' => 'fy_NL'
                ],
                [
                    'key' => 'Yangben',
                    'value' => 'yav'
                ],
                [
                    'key' => 'Yangben (Cameroon)',
                    'value' => 'yav_CM'
                ],
                [
                    'key' => 'Yiddish',
                    'value' => 'yi'
                ],
                [
                    'key' => 'Yiddish (World)',
                    'value' => 'yi_001'
                ],
                [
                    'key' => 'Yoruba',
                    'value' => 'yo'
                ],
                [
                    'key' => 'Yoruba (Benin)',
                    'value' => 'yo_BJ'
                ],
                [
                    'key' => 'Yoruba (Nigeria)',
                    'value' => 'yo_NG'
                ],
                [
                    'key' => 'Zarma',
                    'value' => 'dje'
                ],
                [
                    'key' => 'Zarma (Niger)',
                    'value' => 'dje_NE'
                ],
                [
                    'key' => 'Zulu',
                    'value' => 'zu'
                ],
                [
                    'key' => 'Zulu (South Africa)',
                    'value' => 'zu_ZA'
                ]
            ],
            'width' => '',
            'defaultValue' => null,
            'optionsProviderClass' => null,
            'optionsProviderData' => null,
            'queryColumnType' => 'varchar(190)',
            'columnType' => 'varchar(190)',
            'phpdocType' => 'string',
            'name' => 'localeCode',
            'title' => 'Locale',
            'tooltip' => '',
            'mandatory' => false,
            'noteditable' => true,
            'index' => false,
            'locked' => false,
            'style' => '',
            'permissions' => null,
            'datatype' => 'data',
            'relationType' => false,
            'invisible' => false,
            'visibleGridView' => false,
            'visibleSearch' => false
        ];

        $cart = $this->container->getParameter('coreshop.model.cart.pimcore_class_name');
        $classUpdater = new ClassUpdate($cart);

        if (!$classUpdater->hasField('localeCode')) {
            $classUpdater->insertFieldAfter('store', $localeField);
            $classUpdater->save();
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
