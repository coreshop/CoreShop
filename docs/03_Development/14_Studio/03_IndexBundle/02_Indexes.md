# Indexes in Studio v2

The index editor lets you configure which class fields are written to an index and how.

## Components Structure

```
IndexBundle/Resources/assets/pimcore-studio/src/modules/indexes/
├── IndexManager.tsx          # Main manager using EntityTabbedManager, builds the save payload
├── IndexDetail.tsx           # Tab container (Settings, Columns)
├── api.ts                    # IndexApi extending EntityApi
├── columnNames.ts            # Column name helpers (uniqueness, free name proposal)
└── components/
    ├── ColumnsPanel.tsx      # Split layout: class definition tree | selected fields
    ├── ClassDefinitionTree.tsx
    ├── SelectedFieldsTree.tsx
    └── FieldEditModal.tsx    # Edit a column (name, type, getter, interpreter)
```

## Column Identity

A column is identified by its **name**, not by its class field (`objectKey`):

- The same field may be indexed several times, e.g. with different interpreters.
- Fields of different object bricks, field collections or classification store groups share
  the same `objectKey` and `objectType`; only their getter configuration tells them apart.

The name is the column name of the index table, so it has to be unique within an index
(case-insensitive, like MySQL column names):

- `ColumnsPanel` proposes a free name when a field is added (`getUniqueColumnName`:
  `sku` → `sku_2`).
- `FieldEditModal` rejects a name that another column already uses (`isColumnNameTaken`).
- The backend validates the same with the `UniqueIndexColumnNames` constraint on
  `Index::$columns`.

## Save Payload

`IndexManager` sends `columns` as a plain list. Do not key it by `objectKey` (or any other
field): columns with the same key would overwrite each other in the payload, and the
backend would delete the missing ones (`allow_delete` + orphan removal).
