'use strict';

// Wraps some elements in anchor tags referencing to the Symfony documentation
$(function() {
    const $modal = $('#sourceCodeModal'),
        $controllerCode = $modal.find('code.php'),
        $templateCode = $modal.find('code.twig');

    function anchor(url, content) {
        return '<a class="doclink" target="_blank" href="' + url + '">' + content + '</a>';
    }

    // Wraps links to the Symfony documentation
    $modal.find('.hljs-comment').each(function() {
        $(this).html($(this).html().replace(/https:\/\/symfony.com\/doc\/[\w/.#-]+/g, function(url) {
            return anchor(url, url);
        }));
    });

    // Wraps Symfony's annotations
    const annotations = {
        '@Cache': 'https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/cache.html',
        '@IsGranted': 'https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/security.html#isgranted',
        '@ParamConverter': 'https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html',
        '@Route': 'https://symfony.com/doc/current/routing.html#creating-routes-as-annotations',
        '@Security': 'https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/security.html#security'
    };

    $controllerCode.find('.hljs-doctag').each(function() {
        const annotation = $(this).text();

        if (annotations[annotation]) {
            $(this).html(anchor(annotations[annotation], annotation));
        }
    });

    // Wraps Twig's tags
    $templateCode.find('.hljs-template-tag > .hljs-name').each(function() {
        const tag = $(this).text();

        if ('else' === tag || tag.match(/^end/)) {
            return;
        }

        const url = 'https://twig.symfony.com/doc/3.x/tags/' + tag + '.html#' + tag;

        $(this).html(anchor(url, tag));
    });

    // Wraps Twig's functions
    $templateCode.find('.hljs-template-variable > .hljs-name').each(function() {
        const func = $(this).text(),
            url = 'https://twig.symfony.com/doc/3.x/functions/' + func + '.html#' + func;

        $(this).html(anchor(url, func));
    });
});
