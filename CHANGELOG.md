# Changelog

All notable changes to `filament-zkteco-adms` will be documented in this file.

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
