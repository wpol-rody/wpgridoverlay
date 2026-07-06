# Maandelijkse Release Workflow

Gebruik deze flow om updates klaar te zetten zonder dat elke wijziging meteen live of in de release-zip terechtkomt.

## Branches

- `main`: laatste uitgebrachte versie.
- `develop`: wijzigingen die klaarstaan voor de volgende maandelijkse release.
- `feature/...`: een losse branch per feature of bugfix.

Merge afgeronde feature branches eerst naar `develop`. Pas wanneer je wilt releasen, merge je de geselecteerde en geteste wijzigingen naar `main`.

## Maandelijkse Stappen

1. Kies welke afgeronde wijzigingen deze maand mee mogen.
2. Merge die wijzigingen naar de release branch.
3. Werk het versienummer bij in:
   - De plugin header in `gridly-design-overlay.php`.
   - `WPGO_VERSION` in `gridly-design-overlay.php`.
   - `Stable tag` in `readme.txt`.
4. Voeg een bijpassende changelog-sectie toe in `readme.txt`.
5. Draai:

```bash
composer run release
```

Het script controleert of de versies gelijk staan, valideert PHP syntax, draait PHPCS en bouwt daarna de zip in:

```text
/Users/rody/Desktop/Rody/rodyvdkar.nl/Overlay plugin/gridly-design-overlay.zip
```

## Handige Commands

Alleen controleren, zonder zip te bouwen:

```bash
composer run release:check
```

Alleen de zip bouwen:

```bash
composer run dist
```

De zip lokaal installeren in WordPress doe je alleen wanneer je de release echt lokaal wilt testen of toepassen:

```bash
composer run install-local
```
