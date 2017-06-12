# Pimcore Routes

ResourceBundle takes care of installing routes for you. Simply put them into
```AcmeBundle/Resources/install/pimcore/routes/staticroutes.yml``` and ResourceBundles installs them on ```coreshop:resource:install``` Command.

Example File:
```yml
acme_route:
  pattern: "/(\\w+)\\/acme/"
  reverse: "/%_locale/acme"
  module: AcmeBundle
  controller: "@acme.frontend.controller.controller"
  action: doSomething
  variables: _locale
  priority: 2
```