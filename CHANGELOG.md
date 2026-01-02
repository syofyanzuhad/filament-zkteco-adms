# Changelog

All notable changes to `filament-zkteco-adms` will be documented in this file.

## v1.2.7 - Filament 3 LTS - 2026-01-02

### Filament 3 Long-Term Support Release

This release provides a stable version for users who need to stay on **Filament 3.x**.

#### Requirements

- PHP `^8.2`
- Laravel `^10.0` or `^11.0`
- Filament `^3.0`

#### Installation

```bash
composer require syofyanzuhad/filament-zkteco-adms:^1.0

```
#### Version Compatibility

| Package Version | Filament | Laravel | PHP |
|-----------------|----------|---------|-----|
| `^2.0` | `^4.0` | `^11.0 \| ^12.0` | `^8.2` |
| `^1.0` | `^3.0` | `^10.0 \| ^11.0` | `^8.2` |

#### Upgrading to Filament 4

When you're ready to upgrade to Filament 4, switch to v2.x:

```bash
composer require syofyanzuhad/filament-zkteco-adms:^2.0

```
#### Note

This release reverts unstable Filament 4 migration code that was inadvertently included in v1.2.6. Users on Filament 3 should use this version.

**Full Changelog**: https://github.com/syofyanzuhad/filament-zkteco-adms/compare/v1.2.5...v1.2.7

## v2.0.0 - Filament 4 Support - 2026-01-02

### What's New

This major release adds full support for **Filament 4.x**.

#### Breaking Changes

- Now requires Filament `^4.0`
- Requires Laravel `^11.0` or `^12.0`
- Requires PHP `^8.2`

#### Migration from v1.x

If you're upgrading from v1.x (Filament 3), update your composer requirement:

```bash
composer require syofyanzuhad/filament-zkteco-adms:^2.0


```
#### Staying on Filament 3

If you need to stay on Filament 3.x, use the 1.x branch:

```bash
composer require syofyanzuhad/filament-zkteco-adms:^1.0


```
#### Version Compatibility

| Package Version | Filament | Laravel | PHP |
|-----------------|----------|---------|-----|
| `^2.0` | `^4.0` | `^11.0 \| ^12.0` | `^8.2` |
| `^1.0` | `^3.0` | `^10.0 \| ^11.0` | `^8.2` |

#### Changes

- Updated all components to use Filament 4 namespaces
- Layout components now use `Filament\Schemas\Components`
- Form inputs use `Filament\Forms\Components`
- Table methods updated: `actions()` → `recordActions()`, `bulkActions()` → `toolbarActions()`
- Updated dev dependencies for Laravel 11+ compatibility

**Full Changelog**: https://github.com/syofyanzuhad/filament-zkteco-adms/compare/v1.2.6...v2.0.0

## 1.0.0 - 202X-XX-XX

- initial release
