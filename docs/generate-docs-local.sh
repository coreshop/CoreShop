
echo "Generate CoreShop Docs"

lessc themes/coreshop/less/theme-coreshop.less themes/coreshop/css/theme-coreshop.min.css -x
rm -rf ../docs-build
mkdir ../docs-build
mkdir ../docs-build/docs
cp -R ./ ../docs-build/docs

cd ../docs-build

find . -name 'README.md' -print0 | xargs -0 -n1 bash -c 'mv "$0" "${0/README.md/index.md}"'
find ./docs -type f -name '*.md' -print0 | xargs -0 sed -i '' -e 's/README/index/g'

cd docs

/usr/local/opt/php@7.3/bin/php ~/.composer/vendor/bin/daux generate --destination=generated-docs -s .

cd ../..
