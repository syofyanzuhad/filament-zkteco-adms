# Changelog

All notable changes to `filament-zkteco-adms` will be documented in this file.

## v2.0.2 - 2026-01-03

### Fixed

- Add remaining default fallback values to all config calls (response, device, and events settings) to prevent errors when package config is not published

**Full Changelog**: https://github.com/syofyanzuhad/filament-zkteco-adms/compare/v2.0.1...v2.0.2

## v2.0.0 - Filament 4 Support - 2026-01-03

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

## v1.2.8 - 2026-01-03

### Fixed

- Add default fallback values to model config calls to fix "Class name must be a valid object or a string" error when config is not published

**Full Changelog**: https://github.com/syofyanzuhad/filament-zkteco-adms/compare/v1.2.7...v1.2.8

## v2.0.1 - 2026-01-03

### Fixed

- Add default fallback values to model config calls to fix "Class name must be a valid object or a string" error when config is not published

**Full Changelog**: https://github.com/syofyanzuhad/filament-zkteco-adms/compare/v2.0.0...v2.0.1

## 2.0.1 - 2025-01-03

### Fixed

- Add default fallback values to model config calls to fix "Class name must be a valid object or a string" error when config is not published

**Full Changelog**: https://github.com/syofyanzuhad/filament-zkteco-adms/compare/v2.0.0...v2.0.1

## 2.0.0 - 2025-01-03

### Added

- Full Filament 4.x support

### Changed

- Updated all components to use Filament 4 namespaces
- Layout components (Section, Grid, etc.) now use `Filament\Schemas\Components`
- Form inputs (TextInput, Select, etc.) use `Filament\Forms\Components`
- Table methods updated: `actions()` → `recordActions()`, `bulkActions()` → `toolbarActions()`
- Filter method updated: `form()` → `schema()`
- Updated dev dependencies for Laravel 11+ compatibility

### Breaking Changes

- Now requires Filament `^4.0`
- Now requires Laravel `^11.0` or `^12.0`
- Now requires PHP `^8.2`

**Full Changelog**: https://github.com/syofyanzuhad/filament-zkteco-adms/compare/v1.2.6...v2.0.0

## 1.2.8 - 2025-01-03

### Fixed

- Add default fallback values to model config calls to fix "Class name must be a valid object or a string" error when config is not published

**Full Changelog**: https://github.com/syofyanzuhad/filament-zkteco-adms/compare/v1.2.7...v1.2.8

## 1.2.7 - 2025-01-03

### Fixed

- Reverted unstable Filament 4 migration code from v1.2.6
- Provides stable Filament 3.x support for users not ready to upgrade

**Full Changelog**: https://github.com/syofyanzuhad/filament-zkteco-adms/compare/v1.2.5...v1.2.7

## 1.2.6 - 2024-12-30

### Fixed

- Add fallback class references to model relationships

## 1.2.5 - 2024-12-30

### Fixed

- Update action imports for Filament compatibility

## 1.0.0 - 2024-12-01

- Initial release
