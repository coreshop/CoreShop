<?php

$db = \Pimcore\Db::get();

if (file_exists(PIMCORE_TEMPORARY_DIRECTORY . "/indexes.tmp")) {
    $mapping = [];

    try {
        $indexesSerialized = file_get_contents(PIMCORE_TEMPORARY_DIRECTORY . "/indexes.tmp");
        $indexes = unserialize($indexesSerialized);

        foreach ($indexes as $index) {
            $db->query("DELETE FROM coreshop_indexes WHERE `name` = ?", [$index['name']]);

            unset($index['id']);

            $newIndex = \CoreShop\Model\Index::create();
            $newIndex->setValues($index);

            $configClass = '\CoreShop\Model\Index\Config\\' . ucfirst($index['type']);

            if (\Pimcore\Tool::classExists($configClass)) {
                $configClass = new $configClass();
                $newColumns = [];

                foreach ($index['config']['columns'] as $col) {
                    $columnNamespace = '\\CoreShop\\Model\\Index\\Config\\Column\\';
                    $columnClass = $columnNamespace . ucfirst($col['objectType']);

                    if (\Pimcore\Tool::classExists($columnClass)) {
                        $newCol = new $columnClass();

                        if ($newCol instanceof \CoreShop\Model\Index\Config\Column) {
                            $newCol->setValues($col);
                            $newCol->setColumnType(convertColumnToNewLayout($newIndex->getType(), $col['columnType']));

                            $newColumns[] = $newCol;
                        }
                    }
                }
                $configClass->setColumns($newColumns);
                $newIndex->setConfig($configClass);
            }

            $newIndex->save();
        }
    } catch (\Exception $ex) {
        throw $ex;
    }
}

/**
 * convert old layout to generic layout
 *
 * @param $type
 * @param $column
 * @return string
 */
function convertColumnToNewLayout($type, $column)
{
    $column = strtolower($column);

    if ($type === "mysql") {
        if ($column === "int(1)" || $column === "boolean") {
            return \CoreShop\Model\Index\Config\Column::FIELD_TYPE_BOOLEAN;
        }

        if (strpos($column, "int") === 0) {
            return \CoreShop\Model\Index\Config\Column::FIELD_TYPE_INTEGER;
        }

        if (strpos($column, "float") === 0 || strpos($column, "double") === 0) {
            return \CoreShop\Model\Index\Config\Column::FIELD_TYPE_DOUBLE;
        }

        if (strpos($column, "date") === 0) {
            return \CoreShop\Model\Index\Config\Column::FIELD_TYPE_DATE;
        }

        if (strpos($column, "text") === 0) {
            return \CoreShop\Model\Index\Config\Column::FIELD_TYPE_TEXT;
        }
    } else {
        if ($column === "boolean") {
            return \CoreShop\Model\Index\Config\Column::FIELD_TYPE_BOOLEAN;
        }

        if ($column === "integer") {
            return \CoreShop\Model\Index\Config\Column::FIELD_TYPE_INTEGER;
        }

        if ($column === "double" || $column === "float") {
            return \CoreShop\Model\Index\Config\Column::FIELD_TYPE_DOUBLE;
        }

        if ($column === "date") {
            return \CoreShop\Model\Index\Config\Column::FIELD_TYPE_DATE;
        }
    }

    return \CoreShop\Model\Index\Config\Column::FIELD_TYPE_STRING;
}
