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
//pimcore.helpers.openElement = function (id, type, subtype) {

pimcore.registerNS('coreshop.helpers.x');
pimcore.registerNS('coreshop.util.format.currency');

coreshop.helpers.long2ip = function (ip) {
    if (!isFinite(ip)) {
        return false
    }

    return [ip >>> 24, ip >>> 16 & 0xFF, ip >>> 8 & 0xFF, ip & 0xFF].join('.')
};

coreshop.helpers.constrastColor = function (color) {
    return (parseInt(color.replace('#', ''), 16) > 0xffffff / 2) ? 'black' : 'white';
};

coreshop.helpers.hexToRgb = function (hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? [
        parseInt(result[1], 16),
        parseInt(result[2], 16),
        parseInt(result[3], 16)
    ] : null;
};


coreshop.util.format.currency = function (currency, v) {
    if (currency === undefined || currency === '') {
        return '0';
    }

    return coreshop.util.format.currency_precision(
        currency,
        v,
        pimcore.globalmanager.get('coreshop.currency.decimal_precision'),
        pimcore.globalmanager.get('coreshop.currency.decimal_factor'),
    );
};

coreshop.util.format.currency_precision = function (currency, v, decimalPrecision, decimalFactor) {
    var value = (Math.round((v / decimalFactor) * decimalFactor)) / decimalFactor;
    var options = {
        style: 'currency',
        currency: currency,
        minimumFractionDigits: decimalPrecision
    };
    var numberFormatter = new Intl.NumberFormat(pimcore.globalmanager.get('user').language, options);

    return numberFormatter.format(value);
};

coreshop.util.format.number = function (v) {
    return coreshop.util.format.number_precision(
        v,
        pimcore.globalmanager.get('coreshop.currency.decimal_precision'),
        pimcore.globalmanager.get('coreshop.currency.decimal_factor'),
    );
};

coreshop.util.format.number_precision = function (v, decimalPrecision, decimalFactor) {
    var value = (Math.round((v / decimalFactor) * decimalFactor)) / decimalFactor;
    var options = {
        style: 'decimal',
        minimumFractionDigits: decimalPrecision
    };
    var numberFormatter = new Intl.NumberFormat(pimcore.globalmanager.get('user').language, options);

    return numberFormatter.format(value);
};
