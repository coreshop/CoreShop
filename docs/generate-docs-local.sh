echo "Generate CoreShop Docs"

lessc themes/coreshop/less/theme-coreshop.less themes/coreshop/css/theme-coreshop.min.css -x
/opt/homebrew/opt/php@7.4/bin/php vendor/bin/daux generate --destination=generated-docs -s .
