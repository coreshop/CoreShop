<?php if ($params['html']['disqus'] && $params['html']['disqus']['include']): ?>

    <hr class="disqus-separator">

    <div id="disqus_thread"></div>
    <script>
        <?php
        $pageUrl = $params['html']['disqus']['url_prefix'] . $page['request'] . '.html';
        $identifier = str_replace("_index.md", "README.md", $page['relative_path']);
        ?>

        var disqus_config = function () {
            this.page.url = "<?= $pageUrl ?>";
            this.page.identifier = "<?= $identifier ?>";
        };

        (function() { // DON'T EDIT BELOW THIS LINE
            var d = document, s = d.createElement('script');
            s.src = '//pimcore-org-docs.disqus.com/embed.js';
            s.setAttribute('data-timestamp', +new Date());
            (d.head || d.body).appendChild(s);
        })();
    </script>
    <noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>

<?php endif; ?>
