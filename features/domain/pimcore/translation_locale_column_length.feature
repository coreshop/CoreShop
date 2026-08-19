@domain @pimcore
Feature: Configurable translation locale column length
  In order to support locales with a script subtag (e.g. "zh_Hans")
  As a site administrator
  I need the ORM mapping for every translation entity's "locale" column to
  use the length configured via "core_shop_resource.translation.locale_column_length"

  Scenario: The configured locale column length is applied to a translation entity
    Then the "country" translation entity should map its locale column with a length of 8
