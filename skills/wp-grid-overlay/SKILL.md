---
name: gridly-design-overlay
description: Build, modify, review, and debug the Gridly Design Overlay WordPress plugin using official WordPress Coding Standards and Plugin Handbook best practices. Use when Codex is asked to work on the local `plugins/gridly-design-overlay` plugin, generate WordPress-native PHP/CSS/JS, add admin or front-end overlay behavior, inspect hooks/enqueues/settings, create relevant tooling such as composer.json or phpcs.xml.dist, or verify plugin quality without touching unrelated WordPress core or theme files.
---

# Gridly Design Overlay

## Overview

Use this skill to build maintainable, secure, WordPress-native plugin code for `wp-content/plugins/gridly-design-overlay`. Treat WordPress core, themes, uploads, and other plugins as read-only context unless the user explicitly asks to change them.

## Working Path

- Start from `/Users/rody/Local Sites/wpgridoverlay/app/public/wp-content`.
- Keep plugin code under `plugins/gridly-design-overlay/`.
- Put Codex skill files under `plugins/gridly-design-overlay/skills/gridly-design-overlay/`; do not mix runtime plugin code into the skill folder.
- If the plugin is still empty, create the normal plugin entry file at `plugins/gridly-design-overlay/gridly-design-overlay.php` before adding assets.

## Required Plugin Structure

Create and maintain this baseline structure for new plugin work:

```text
gridly-design-overlay/
  gridly-design-overlay.php
  includes/
  assets/
  languages/
  composer.json
  phpcs.xml.dist
  .gitignore
```

- `gridly-design-overlay.php` must contain the WordPress plugin header, an `ABSPATH` guard, plugin constants, and only lightweight bootstrapping.
- `includes/` holds PHP classes, functions, admin modules, front-end modules, and integration code once the plugin grows.
- `assets/` holds front-end and admin CSS/JS/images.
- `languages/` holds translation files and matches `Domain Path: /languages`.
- Keep generated dependencies and development-only tooling out of runtime bootstrap code.

## Implementation Workflow

1. Inspect the plugin directory first with `rg --files plugins/gridly-design-overlay` or `find plugins/gridly-design-overlay -maxdepth 3 -type f`.
2. Identify whether the task affects runtime PHP, front-end CSS/JS, admin settings, or Codex skill/plugin metadata.
3. Keep runtime changes scoped to the WordPress plugin folder. Avoid editing `wp-admin`, `wp-includes`, bundled WordPress files, or active themes unless asked.
4. Use WordPress hooks, filters, APIs, and enqueue functions instead of hacks, direct core changes, or large inline assets.
5. Prefer simple, WordPress-conform code over abstract enterprise patterns.
6. Prefer small, readable files over a single growing plugin file once behavior expands.
7. Add activation, deactivation, or uninstall code only when the feature genuinely needs setup, teardown, or cleanup.
8. For a new or missing plugin structure, create `gridly-design-overlay.php`, `includes/`, `assets/`, `languages/`, `composer.json`, `phpcs.xml.dist`, and `.gitignore` in the plugin root by default.
9. Verify PHP syntax, WPCS compliance when tooling exists, and any available test runner before finishing. If no automated test runner exists, say what manual WordPress checks remain.

## Plugin Conventions

- Use the text domain `gridly-design-overlay`.
- Make all user-facing strings translatable with the `gridly-design-overlay` text domain.
- Prefix functions, classes, hooks, handles, options, and nonces with `wpgo_`, `WPGO_`, or `gridly-design-overlay` to avoid collisions.
- Give files, classes, and functional parts names that match their responsibility.
- Use `plugins_url()` or `plugin_dir_url()` for asset URLs and `plugin_dir_path()` for local includes.
- Register public assets on `wp_enqueue_scripts`; register admin assets on `admin_enqueue_scripts`.
- Store settings through the Settings API when adding configurable grid values.
- Sanitize all input with helpers such as `sanitize_text_field()`, `absint()`, `rest_sanitize_boolean()`, `sanitize_key()`, `sanitize_hex_color()`, or a custom callback.
- Escape all output with the narrowest WordPress helper available, such as `esc_html()`, `esc_attr()`, `esc_url()`, and `wp_kses_post()`.
- Use nonce checks for forms, admin actions, AJAX, and REST mutations.
- Use capability checks for admin functionality. Choose the narrowest appropriate capability instead of assuming `manage_options` is always right.
- Write PHP according to WordPress Coding Standards.

## Tooling Files

Keep these control files in `plugins/gridly-design-overlay/` unless the user asks for a different project layout:

- `composer.json`: include `dealerdirect/phpcodesniffer-composer-installer`, `wp-coding-standards/wpcs`, and `phpcompatibility/phpcompatibility-wp` as dev dependencies; add `"lint": "phpcs"` and `"lint:fix": "phpcbf"` scripts.
- `phpcs.xml.dist`: scan the plugin root with `WordPress-Core`, `WordPress-Docs`, `WordPress-Extra`, and `PHPCompatibilityWP`; exclude `vendor/`, `node_modules/`, `skills/`, and `.codex-plugin/`; enforce PHP `7.4-`, the `gridly-design-overlay` text domain, and `wpgo`/`WPGO` prefixes.
- `.gitignore`: ignore generated dependency folders, local editor metadata, lock files if this plugin is not committing dependency locks, logs, and temporary files.

When these files already exist, update them instead of replacing unrelated user choices.

## Output Format

When generating new plugin code or larger changes:

1. Start with a short file structure.
2. Provide the code per file.
3. Briefly explain why each file exists.
4. Prefer WordPress conventions over generic architecture patterns.

## Starter Prompt

Use this shape for new WordPress plugin requests, then adapt the plugin name, slug, text domain, features, and paths to the user's actual project:

```text
Use $gridly-design-overlay to create a WordPress plugin named "Rody Custom Admin Tools".

Requirements:
- Use WordPress Coding Standards.
- Generate a plugin root file with a correct WordPress plugin header.
- Use the baseline plugin structure with includes/, assets/, languages/, composer.json, phpcs.xml.dist, and .gitignore.
- Add an admin settings page under Settings.
- Use nonce and capability checks.
- Save settings through the WordPress Settings API.
- Make all user-facing strings translatable with text domain "rody-custom-admin-tools".
- Generate composer.json and phpcs.xml.dist for PHPCS, WPCS, and PHPCompatibilityWP.
- Output the file structure first, then the code per file, then a short explanation for why each file exists.
- Finish with a self-check for WPCS violations, missing escaping, missing sanitizing, missing nonces, missing capability checks, and missing i18n.
```

For this plugin, replace example values with:

- Plugin name: `Gridly Design Overlay`
- Plugin slug and text domain: `gridly-design-overlay`
- Function prefix: `wpgo_`
- Class prefix: `WPGO_`

## Overlay Behavior

When implementing grid overlay UI:

- Make the overlay disabled by default for normal visitors unless the user asks for always-on behavior.
- Prefer a clearly named toggle, query parameter, admin bar control, keyboard shortcut, or development-only setting.
- Keep overlay elements non-invasive: use predictable z-index, `pointer-events: none` for visual guides, and avoid changing page layout.
- Use CSS custom properties for columns, gutters, max width, colors, and opacity when configurable values are useful.
- Keep visual defaults legible across light and dark pages without dominating the page.

## Verification

Run the narrowest useful checks for the files changed:

```bash
php -l plugins/gridly-design-overlay/gridly-design-overlay.php
```

If more PHP files are added, lint each changed PHP file. When WPCS tooling is available, run the configured PHPCS command, usually through Composer.

## Required Self-Check

Always finish WordPress plugin work by checking the code for:

- WPCS violations.
- Missing escaping.
- Missing sanitizing.
- Missing nonces.
- Missing capability checks.
- Missing i18n for user-facing strings.
- No unused code introduced by the change.

Run `composer run lint` after dependencies are installed. If PHPCS cannot run because dependencies or local PHP are unavailable, still perform the self-check manually and report the tooling blocker.

When changing front-end behavior, load the Local WordPress site and verify that the overlay can be enabled, disabled, and rendered without console errors.
