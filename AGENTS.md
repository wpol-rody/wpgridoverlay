# AGENTS.md

Guidance for AI agents working in this repository.

## Project

This repository contains the `layout-grid-overlay` WordPress plugin. Build and maintain it as a small, WordPress-native plugin that follows the official WordPress Coding Standards and WordPress Plugin Handbook practices.

## Scope

- Work only inside this plugin repository unless the user explicitly asks otherwise.
- Treat WordPress core, themes, uploads, and other plugins as external context.
- Keep runtime plugin code separate from Codex support files under `skills/` and `.codex-plugin/`.
- Prefer simple WordPress conventions over generic enterprise patterns.

## Baseline Structure

Maintain this structure:

```text
layout-grid-overlay/
  layout-grid-overlay.php
  includes/
  assets/
  languages/
  composer.json
  phpcs.xml.dist
  .gitignore
```

- `layout-grid-overlay.php`: plugin header, `ABSPATH` guard, constants, and lightweight bootstrap only.
- `includes/`: PHP modules, classes, admin code, front-end code, integrations.
- `assets/`: CSS, JavaScript, images, and other runtime assets.
- `languages/`: translation files. Keep `Domain Path: /languages`.
- `skills/`: Codex skill files, not runtime plugin code.

## WordPress Rules

- Use WordPress hooks, filters, APIs, and enqueue functions.
- Do not modify WordPress core.
- Use text domain `layout-grid-overlay` for all user-facing strings.
- Prefix globals, functions, hooks, options, handles, and nonces with `wpgo_` or `layout-grid-overlay`.
- Prefix classes and constants with `WPGO_`.
- Sanitize all input.
- Escape all output with the narrowest appropriate WordPress escaping helper.
- Use nonce checks for forms, admin actions, AJAX, and REST mutations.
- Use capability checks for admin behavior.
- Add activation, deactivation, or uninstall logic only when required.
- Make code readable and WPCS-compliant before adding abstractions.

## Tooling

Use the Composer scripts when dependencies are installed:

```bash
composer run lint
composer run lint:fix
```

The PHPCS ruleset lives in `phpcs.xml.dist` and should check WordPress Core, Docs, Extra, and PHPCompatibilityWP standards.

If Composer dependencies or local PHP are unavailable, still do a manual review and report the blocker clearly.

## Output Style

For new features or larger changes, respond in this order:

1. Short file structure.
2. Code or change summary per file.
3. Short explanation of why each file exists.
4. Validation and self-check results.

## Required Self-Check

Before finishing any WordPress plugin work, check for:

- WPCS violations.
- Missing escaping.
- Missing sanitizing.
- Missing nonces.
- Missing capability checks.
- Missing i18n for user-facing strings.
- Unused code.

Report which checks ran and which checks could not run.

## Useful Skill

When available, use the local Codex skill:

```text
$layout-grid-overlay
```

It contains the project-specific WordPress plugin builder workflow and should be kept aligned with this file.
