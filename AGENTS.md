# Repository Guidelines

## Project Structure & Module Organization
- Source: `src/CoreShop/Bundle/*` (Symfony bundles) and `src/CoreShop/Component/*` (domain components).
- Config: `config/` (services, routes), app kernels in `src/*Kernel.php`.
- Public assets: `public/`.
- Studio UI plugins (TypeScript/React): `src/*/Resources/assets/pimcore-studio` (npm workspaces).
- Tests: Behat features in `features/`; perf script in `tests/k6.js`.
- Docs and templates: `docs/`, `templates/`.

## Build, Test, and Development Commands
- PHP deps: `composer install` (or `docker compose run --rm php composer install`).
- App console: `bin/console` (Symfony/Pimcore CLI), e.g. `bin/console cache:clear`.
- Studio UI build: `npm run dev` (watch) or `npm run build` (prod) at repo root. Node ≥ 18.
- Static analysis: `vendor/bin/phpstan analyse -c phpstan.neon src` and `vendor/bin/psalm`.
- Coding standard: `vendor/bin/ecs check src --fix` (see `ecs.php`).
- Behat domain: `docker compose run --rm behat vendor/bin/behat -c behat.yml.dist -p default`.
- Behat UI: `docker compose run --rm behat` (requires a running instance).

## Coding Style & Naming Conventions
- PHP: PSR-12 + project rules via ECS. 4-space indent; strict types where practical; services autowired/aliased in YAML.
- Namespaces: `CoreShop\Bundle\<BundleName>` and `CoreShop\Component\<Context>`; tests and features mirror package structure.
- TypeScript/React: follow workspace lint configs; prefer functional components and `camelCase` files; bundle names kebab-case.

## Testing Guidelines
- Primary framework: Behat (`features/`), profiles in `behat.yml.dist`.
- Add domain scenarios under `features/domain/` and UI under `features/ui/`.
- Keep scenarios independent; use tags for slow/browser tests.
- For static checks, ensure PHPStan and Psalm pass before PR.

## Commit & Pull Request Guidelines
- Commits: imperative mood with scope, e.g. `[Product] fix price rounding` or `[Studio] improve menu state]`.
- PRs: clear description, linked issues, steps to test; include screenshots/GIFs for Studio UI.
- Keep changes scoped; update docs/config when relevant.
- CLA required (see `CLA.md`). Security issues: email `info@coreshop.com`.

## Security & Configuration Tips
- Use `.env.local` for secrets; don’t commit `var/`, `vendor/`, or `node_modules/`.
- PHP 8.3+, Pimcore 12+. Run via Docker (`docker compose build` then `docker compose run --rm php bin/install`) for a clean setup.
