$(document).ready(function() {
    var Processor = (function() {
        var currentSection = 0;

        var languageMapping = {
            php: 'PHP',
            twig: 'Twig',
            yml: 'YAML',
            yaml: 'YAML',
            unknown: 'Code'
        };

        var getCodeLanguage = function (code) {
            var language = 'unknown';
            var classes = code.attr('class').split(' ');
            $.each(classes, function(idx, val) {
                var match = val.match(/^language\-(.+)$/);
                if (match) {
                    language = match[1];
                }
            });

            return language;
        };

        var getLanguageTitle = function (language) {
            if ('undefined' !== languageMapping[language]) {
                return languageMapping[language];
            }

            return language.toUpperCase();
        };

        var createContainer = function (sectionId, children) {
            var childTabs = [];

            children.each(function() {
                var pre = $(this);
                var code = pre.find('> code');

                if (code.length !== 1) {
                    throw new Error('Failed to find code element for code section');
                }

                var language = getCodeLanguage(code);

                childTabs.push({
                    el: pre,
                    language: language
                });
            });

            var tabNav = $('<ul class="code-section--tab-nav nav nav-tabs" role="tablist" />');
            var tabContent = $('<div class="code-section--tab-content tab-content" />');
            var tabContainer = $('<div class="code-section--tabs" />')
                .append(tabNav)
                .append(tabContent);

            $.each(childTabs, function (idx, childTab) {
                var identifier = 'code-section-' + sectionId + '-' + childTab.language + '-' + idx;

                var li = $('<li role="presentation" />')
                    .appendTo(tabNav);

                var a = $('<a role="tab" />')
                    .attr('href', '#' + identifier)
                    .attr('aria-controls', identifier)
                    .text(getLanguageTitle(childTab.language))
                    .appendTo(li);

                var pane = $('<div class="code-section--tab-pane tab-pane" role="tabpanel" />')
                    .attr('id', identifier)
                    .append(childTab.el.detach())
                    .appendTo(tabContent);

                if (0 === idx) {
                    li.addClass('active');
                    pane.addClass('active');
                }

                a.on('click', function(e) {
                    e.preventDefault();

                    tabNav.find('> li').removeClass('active');
                    li.addClass('active');

                    tabContent.find('> .tab-pane').removeClass('active');
                    pane.addClass('active');
                });
            });

            return tabContainer;
        };

        return {
            process: function (sectionId, section) {
                var children = section.children();

                var doProcess = true;
                children.each(function() {
                    if ('pre' !== $(this).prop('tagName').toLowerCase()) {
                        window.console && console.error('Not transforming section into tabbed block as a non-pre child was found.');

                        doProcess = false;
                        return false;
                    }
                });

                if (!doProcess) {
                    return;
                }

                try {
                    var container = createContainer(sectionId, children);
                } catch (e) {
                    window.console && console.error('Failed to create code section', e);
                    return;
                }

                section.append(container);
            }
        };
    }());

    $('.code-section').each(function(idx, section) {
        Processor.process(idx, $(this));
    });
});