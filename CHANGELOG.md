# Changelog

All notable changes to `filament-zkteco-adms` will be documented in this file.

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
