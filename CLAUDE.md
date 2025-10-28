# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

CoreShop is a Symfony-based Pimcore eCommerce platform built with a modular bundle/component architecture. The codebase follows Domain-Driven Design principles and is structured as a monorepo containing multiple related packages.

## Commands

### Development Commands

```bash
# Validate code syntax and configuration
bin/console lint:yaml src
bin/console lint:twig src
bin/console lint:container
bin/console doctrine:schema:validate --skip-sync

# Static Analysis
vendor/bin/phpstan                    # PHPStan analysis (level 3)
vendor/bin/psalm                      # Psalm static analysis

# Code Style
vendor/bin/ecs                        # Easy Coding Standard
vendor/bin/ecs --fix                  # Fix coding standard issues

# Testing
vendor/bin/behat                      # Run Behat tests
vendor/bin/behat --profile=default    # Run specific Behat profile

# Validation
composer validate                     # Validate composer.json
```

### Database & Cache
```bash
bin/console cache:clear --env=dev
bin/console doctrine:migrations:migrate
bin/console pimcore:install
```

## Architecture

### Bundle-Component Pattern
CoreShop follows a strict Bundle-Component separation pattern:
- **Components** (`src/CoreShop/Component/`): Domain logic, business rules, interfaces
- **Bundles** (`src/CoreShop/Bundle/`): Symfony integration, DI configuration, controllers

### Core Architecture Layers

#### Components (Business Logic)
- `Core/`: Central business logic and models
- `Product/`: Product management and catalog functionality  
- `Order/`: Order processing and cart management
- `Customer/`: Customer and user management
- `Payment/`: Payment processing abstractions
- `Shipping/`: Shipping calculation and management
- `Index/`: Search and indexing functionality
- `Currency/`: Multi-currency support
- `Address/`: Address management
- `Store/`: Multi-store functionality
- `Taxation/`: Tax calculation rules
- `Rule/`: Business rule engine

#### Bundles (Symfony Integration)
- `CoreBundle/`: Main bundle providing core services
- `AdminBundle/`: Pimcore admin interface integration
- `FrontendBundle/`: Frontend controllers and templates
- `ResourceBundle/`: Generic CRUD operations
- Corresponding bundles for each Component (e.g., `ProductBundle/`, `OrderBundle/`)

### Key Design Patterns
- **Factory Pattern**: Extensive use for object creation
- **Specification Pattern**: Business rules and validation
- **Event-Driven Architecture**: Symfony events for extensibility
- **Repository Pattern**: Data access layer
- **State Machine**: Order and payment workflows

## Configuration

### Environment Files
- `.env`: Base environment configuration
- `.env.local`: Local overrides (not committed)

### Key Configuration Files
- `config/`: Symfony configuration
- `phpstan.neon`: Static analysis configuration (level 3)
- `psalm.xml`: Psalm configuration
- `ecs.php`: Coding standards configuration
- `behat.yml.dist`: BDD testing configuration

## Testing Strategy

### Test Types
- **Behat**: BDD integration tests for business scenarios
- **PHPUnit**: Unit tests (configured via composer.json)
- **Static Analysis**: PHPStan (level 3) and Psalm

### Test Environment
- Uses MySQL database for integration tests
- Pimcore test environment with specific kernel (`BehatKernel.php`)
- Environment variables required for Pimcore licensing

## Development Workflow

### Code Quality Standards
- PHP 8.3+ required
- Strict PSR-12 coding standards via ECS
- PHPStan level 3 analysis required
- All YAML, Twig, and container configuration must be valid
- Doctrine schema validation required

### Branch Strategy
- Main branches: `4.0`, `4.1`, `5.0`, `next`
- Base branch for PRs: `master`

### Before Committing
Always run the full validation suite:
```bash
composer validate
bin/console lint:yaml src
bin/console lint:twig src
bin/console lint:container
bin/console doctrine:schema:validate --skip-sync
vendor/bin/phpstan
vendor/bin/psalm
vendor/bin/ecs
```

## Pimcore Integration

This is a Pimcore Bundle project requiring:
- Pimcore ^12.0
- Specific Pimcore environment variables and licensing
- Integration with Pimcore's data objects and admin interface
- Pimcore-specific kernels for different environments

## Key Dependencies

- **Symfony**: 6.3+ or 7.0+ for framework components
- **Doctrine ORM**: 3.0+ for data persistence
- **Payum**: Payment processing integration
- **Sylius ThemeBundle**: Theme management
- **JMS Serializer**: API serialization
- **KnpMenuBundle**: Navigation management


## Knowledge Graph
Use the knowledge-graph-mcp before and after every task you do.