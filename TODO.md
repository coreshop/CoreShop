# Studio v2 Migration — offene Punkte

Stand: 2026-04-16. Enthält nur Features, die in der alten ExtJS-Admin-UI existieren
und in Pimcore Studio v2 noch kein Äquivalent haben.

## Cart Detail-View

Eigenständiges Cart-Management getrennt vom Order-Detail — wird für
Kundenservice-Workflows benötigt (Cart einsehen, bevor eine Order daraus wird).

**ExtJS-Quellen:**
- `src/CoreShop/Bundle/OrderBundle/Resources/public/pimcore/js/cart/detail/` (Panel, Shell)
- `src/CoreShop/Bundle/OrderBundle/Resources/public/pimcore/js/cart/detail/blocks/`
  (comments, correspondence, customer, detail, header, info)
- `src/CoreShop/Bundle/CoreBundle/Resources/public/pimcore/js/cart/detail/blocks/` (Core-Extensions)

**Studio-Status:** Nur Order-Detail unter `OrderBundle/.../modules/sales/` vorhanden,
kein Cart-Modul.

**Umsetzung:**
- Neues Studio-Modul `OrderBundle/.../modules/carts/` nach demselben Muster wie
  `modules/sales/` (Tabs: Header, Info, Customer, Detail, Comments, Correspondence)
- Listing-Builder für Cart-Listen
- Keine Invoice/Shipment/Payment-Tabs (Cart ist pre-Order)

## Object Grid Column Operators

**Architektur-Befund:** Studio v2 hat KEINE direkte Entsprechung der ExtJS-
Operator-Tree-Architektur. Die neue API unterscheidet zwischen:

1. **`TransformerInterface`** (Tag `pimcore.studio_backend.grid_transformer`)
   — reine Pipeline-Transformationen auf `AdvancedValue[]`, **kein Element-Zugriff**
2. **`ColumnCollectorInterface` + `ColumnResolverInterface`** (Tags
   `pimcore.studio_backend.grid_column_collector` / `grid_column_resolver`)
   — neue Spaltentypen im Column-Picker, **mit Element-Zugriff**

Die alten `classic_admin.yml`-Einträge sind ohnehin tot (kein
`admin-ui-classic-bundle` in composer.json, Loader-Gate auf
`PimcoreAdminBundle` feuert nie).

Siehe `plans/kind-spinning-frost.md` für ausführliche Recherche.

### Status der 4 Operatoren

| Operator | Studio v2 Modell | Status |
|---|---|---|
| `coreshop_order_state` | Transformer | ✅ **Backend + Frontend fertig** (`OrderBundle/Grid/Column/Transformer/OrderState.php`, Frontend-Pipeline + Config-UI mit Workflow-Select + highlightLabel-Switch) |
| `coreshop_price_formatter` | Transformer (mit Semantikänderung) | ✅ **Backend + Frontend fertig** (`OrderBundle/Grid/Column/Transformer/PriceFormatter.php`, Frontend-Pipeline + Config-UI). Breaking Change gegenüber Classic: Currency muss jetzt explizit konfiguriert werden (via `currencyIsoCode` oder `currencyField`), statt implizit aus `$element->getStore()->getCurrency()` |
| `StorePrice` | Collector + Resolver + Transformer | ✅ **Fertig** — zwei Wege: (1) `StorePriceCollector` + `StorePriceResolver` + `StorePriceDefinition` in CoreBundle: 1-Klick “Store Price (Storename)” Spalten im Column-Picker, fertig formatiert. (2) `StoreValuesField` Transformer in CoreBundle: Advanced-Column-Route für Power-User (Store + Field konfigurierbar). `StoreValuesColumnDefinition` fixt den “Key not found”-Crash für `coreShopStoreValues` als Source-Field. |
| `coreshop_resource_field_getter` | — | ⏸️ **Ersatz vorhanden**: Studio v2 hat native `AdvancedColumnConfig/RelationFieldConfig` zum Relation-Traversal. Kein Custom-Port nötig, stattdessen User-Dokumentation für den neuen Weg |

### Offene Arbeit

1. **ResourceFieldGetter-Doku**
   - User-Doku ergänzen: wie man Resource-Felder in Studio-v2-AdvancedColumn
     via RelationFieldConfig adressiert (kein Custom-Operator mehr nötig)

### Offene Entscheidungen

- **HTML-Rendering in OrderState-Transformer**: Der `highlightLabel`-Modus gibt
  `<span style=”background-color:...”>label</span>` zurück. Ob Studio v2 Grid
  HTML rendert oder escaped, muss verifiziert werden — ggf. Umstieg auf ein
  Rich-Cell-Rendering im Frontend statt HTML-String vom Backend.
- **Per-Column-Config-UI**: Pimcore Studio erlaubt aktuell nur für Advanced Columns
  eine aufklappbare Config-UI (`column.key === 'advanced'` hart geprüft in
  `grid-config-list.tsx:41`). Für reguläre Spalten (z.B. storeValues) gibt es
  keinen Extension-Punkt — Feature-Request an Pimcore upstream sinnvoll.

## Variant Unit Definition Solidifier

Product-Save-Hook: Wenn ein Produkt mit Unit-Definitions gespeichert wird und sich
diese geändert haben, fragt die UI, ob die Änderung auf alle Varianten propagiert
(„solidifiziert”) werden soll.

**ExtJS-Quelle:**
- `src/CoreShop/Bundle/CoreBundle/.../js/workflow/variantUnitDefinitionSolidifier.js`

**Backend-Endpoints (bestehen bereits):**
- `GET coreshop_admin_purchsable_variant_unit_solidifier_check` — prüft, ob
  Solidifizierung nötig ist, liefert Strategie oder Error-Status
- `PUT coreshop_admin_purchsable_variant_unit_solidifier_apply` — wendet die
  Solidifizierung an, gibt Liste betroffener Varianten zurück

**Umsetzung Studio v2:**
- Als Entity-Lifecycle-Hook (`afterSave`) auf dem Product-Entity registrieren
  (siehe CLAUDE.md: `entityLifecycleHooksServiceId`)
- Oder als Save-Decorator, der nach erfolgreichem Save das Check-Endpoint aufruft
  und ggf. einen Confirmation-Dialog öffnet
- Bei Bestätigung: Apply-Endpoint aufrufen, betroffene Tabs neu laden
- Übersetzungskeys `coreshop_solidify_variant_unit_definition_data_*` sind bereits
  vorhanden