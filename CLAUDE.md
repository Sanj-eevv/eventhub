# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Laravel 12 event management app using Inertia.js v2, Vue 3 (Composition API, `<script setup>`), TypeScript, and Tailwind CSS v4. UI components from shadcn-vue (new-york style, reka-ui primitives, lucide-vue-next icons).

## Commands

```bash
composer setup          # Full project setup (install, env, key, migrate, npm install, build)
composer dev            # Dev server (artisan serve + queue + pail + vite HMR)
composer test           # Clear config cache + run tests
./vendor/bin/pest --filter=name  # Run a single test
./vendor/bin/pint       # Fix PHP code style
npx eslint resources/   # Lint frontend
npm run build           # Production build
```

## Architecture

- **No Kernel.php** — Laravel 12 uses functional bootstrap in `bootstrap/app.php` for middleware and routing
- **Inertia SPA pattern** — controllers return `Inertia::render('PageName')`, no separate API routes
- **Wayfinder** — auto-generates typed TypeScript route actions in `resources/js/actions/` and `resources/js/routes/` from Laravel routes. Vue components import these instead of using magic URL strings
- **Page resolution** — `resources/js/app.ts` resolves Vue pages from `resources/js/pages/**/*.vue`
- **Shared props** — `HandleInertiaRequests` middleware shares global data to all Inertia pages
- **Path alias** — `@/*` maps to `resources/js/*`
- **Tailwind v4** — uses Vite plugin (`@tailwindcss/vite`), no `tailwind.config.js`

## Testing

- **Pest v4** with Laravel plugin
- SQLite in-memory for tests (configured in `phpunit.xml`)
- `RefreshDatabase` trait is available but not enabled by default in `tests/Pest.php`

## Code Style Conventions

### PHP (enforced by Pint, PER preset)

- `declare(strict_types=1)` in all files
- `final` on all classes
- Strict comparison (`===`), no Yoda style
- Single-action controllers use `__invoke()`
- Arrow functions preferred, alphabetical imports

### TypeScript/Vue (enforced by ESLint)

- `import/order` enforced (alphabetical, grouped)
- `consistent-type-imports` required
- `resources/js/components/ui/*` excluded from linting (generated shadcn-vue)

## Database

MySQL (`event` database). Sessions, cache, and queue all use database driver.

## Laravel Code Optimization

- Always run the **laravel-simplifier:laravel-simplifier** plugin on every Laravel/PHP code snippet you generate to ensure the code is clean, optimized, and follows Laravel best practices.”
- Do not write any comments in the generated code. No comments are required under any circumstances.
