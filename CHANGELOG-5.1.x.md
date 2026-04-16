## 5.1.0

### Pimcore Studio v2

CoreShop 5.1 introduces support for Pimcore Studio. Both Studio and the ExtJS-based Classic Admin 
are now supported as **optional, independent installations** — install either, 
or both side-by-side. Existing Classic-Admin-only installations continue to work without
changes.

### Classic-Admin internals moved to `AdminClass/` subnamespace

Internal helper classes that exist solely to integrate with the ExtJS Classic Admin
(grid column config operators, admin-JS injection listeners, admin-grid filter listeners, etc.)
have been moved into a dedicated `AdminClass/` subnamespace within their bundles.

This is a preparatory step: it isolates Classic-Admin-specific code so it can be cleanly removed
in a future major version once Studio v2 covers all functionality. **The classes still exist and
Classic Admin continues to work without changes** — only the fully-qualified namespace changed.

If you imported any of these classes directly via `use` statements or referenced them by FQCN as
service IDs in your own configuration, see [UPGRADE-5.1.md](UPGRADE-5.1.md) for the rename mapping.
