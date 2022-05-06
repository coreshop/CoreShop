;(function ($) {
    $.coreshopVariantSelector = function (attributeContainer) {
        let _attributeContainer = undefined;
        let _config = {};
        let _attributeGroups = [];

        let _clearGroup = function(group) {
            delete group.selected;
            group.elements.forEach((element) => {
                element.disabled = true;
                element.checked = false;

                // remove options on select
                if (element.tagName.toLowerCase() === 'select') {
                    const options = element.querySelectorAll('option:not([value=""])');
                    options.forEach((option) => {
                        element.removeChild(option);
                    });
                }
            });
        }

        let _clearGroups = function(group) {
            do {
                _clearGroup(group);
                group = group.nextGroup;
            } while (group);
        }

        let _filterAttributes = function(attributes, group, previousGroup) {
            if (previousGroup === undefined) {
                previousGroup = group;
            }

            if (previousGroup.prevGroup) {
                attributes = _filterAttributes(attributes, group, previousGroup.prevGroup);
            }

            if (previousGroup === group) {
                return attributes;
            }

            let filterAttributes = [];
            if (previousGroup.selected) {
                attributes.forEach((attribute) => {
                    attribute.products.forEach((product) => {
                        if (_config.index.hasOwnProperty(product.id) &&
                            _config.index[product.id]['attributes'].hasOwnProperty(previousGroup.group.id) &&
                            _config.index[product.id]['attributes'][previousGroup.group.id] === previousGroup.selected &&
                            !filterAttributes.includes(attribute)
                        ) {
                            filterAttributes.push(attribute);
                        }
                    });
                });
                return filterAttributes;
            }

            return attributes;
        }

        let _configureGroup = function(group) {
            let attributes = group.attributes.slice();
            attributes = _filterAttributes(attributes, group);

            if (attributes) {
                attributes.forEach((attribute) => {
                    group.elements.forEach((element) => {
                        // set options on select, otherwise only enable inputs
                        if (element.tagName.toLowerCase() === 'select') {
                            const option = new Option(attribute.attribute.name, attribute.attribute.id);
                            option.id = 'attribute-' + attribute.attribute.id;
                            if (group.selected === attribute.attribute.id) {
                                option.selected = true;
                            }
                            element.add(option);
                            element.disabled = false;
                        } else {
                            if (parseInt(element.dataset.group) === group.group.id && parseInt(element.value) === attribute.attribute.id) {
                                element.disabled = false;

                                if (group.selected === attribute.attribute.id) {
                                    element.checked = true;
                                }
                            }
                        }
                    });
                });
            }
        }

        let _setupAttributeGroupSettings = function() {
            let index = _attributeGroups.length;
            let group;

            while (index--) {
                group = _attributeGroups[index];
                group.prevGroup = _attributeGroups[index - 1];
                group.nextGroup = _attributeGroups[index + 1];

                if (!index || group.selected) {
                    _configureGroup(group);
                } else {
                    _clearGroup(group);
                }
            }
        }

        let _setupChangeEvents = function() {
            _attributeGroups.forEach((group) => {
                group.elements.forEach((element) => {
                    element.onchange = (e) => {
                        _configureElement(group, element);
                    };
                });
            });
        }

        let _init = function(attributeContainer) {
            if(!attributeContainer) {
                return;
            }

            _attributeContainer = attributeContainer;
            _config = JSON.parse(_attributeContainer.dataset.config);
            _config.attributes.forEach((group) => {
                group.elements = _attributeContainer.querySelectorAll('[data-group="' + group.group.id + '"]');
                _attributeGroups.push(group)
            });

            _setupAttributeGroupSettings();
            _setupChangeEvents();
        }

        let _redirectToVariant = function() {
            const groups = _attributeGroups.filter((g) => g.selected);

            const selected = Object.fromEntries(
                groups.map((g) => {
                    return [g.group.id, g.selected];
                })
            );

            const filtered = Object.values(_config.index).filter((p) => {
                return JSON.stringify(p.attributes) === JSON.stringify(selected);
            });

            // length should always be 1, but let's check it
            if (filtered.length === 1 && filtered[0]['url']) {
                window.location.href = filtered[0]['url'];
            }
        }

        let _createEvent = function(name, data = {}) {
            return new CustomEvent('variant_selector.' + name, {
                bubbles: true,
                cancelable:false,
                data: data
            })
        }

        let _configureElement = function(group, element) {
            _attributeContainer.dispatchEvent(
                _createEvent('change', { element: element })
            );

            if (element.value) {
                group.selected = parseInt(element.value);
                if (group.nextGroup) {
                    _attributeContainer.dispatchEvent(
                        _createEvent('select', { element: element })
                    )
                    _clearGroups(group.nextGroup);
                    _configureGroup(group.nextGroup);
                } else {
                    _attributeContainer.dispatchEvent(_createEvent('redirect', { element: element }));
                    _redirectToVariant();
                    return;
                }
            } else {
                delete group.selected;
                if (group.nextGroup) {
                    _clearGroups(group.nextGroup);
                }
            }
        }

        _init(attributeContainer);
    };
})(jQuery);