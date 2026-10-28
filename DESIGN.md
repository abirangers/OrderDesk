# Design direction

Internal order desk for a small distributor. Used for hours at a time by two to six
people, not browsed once by a visitor. Every choice below follows from that.

## Identity

Ledger, not dashboard. The reference is a paper order book: dense rows, ruled lines,
numbers that align, nothing that moves. A dashboard is read once; a ledger is read all
day.

## Palette

| Token | Value | Why |
|---|---|---|
| `paper` | `#F7F6F3` | Warm off-white. Pure white at full-screen brightness for eight hours is tiring. |
| `surface` | `#FFFFFF` | Only for the table body and the form panel, so content separates from the page without a shadow. |
| `ink` | `#1A1A18` | Near-black, slightly warm. Pure `#000` on a warm background reads as a rendering error. |
| `ink-muted` | `#6B6862` | Secondary labels. Passes 4.5:1 on `paper`. |
| `rule` | `#E3E0D9` | Hairline borders. One weight, everywhere. |
| `oxide` | `#B4462A` | The single accent: primary action, current status. One accent used sparingly is a signal; one accent on every element is noise. |

Status colours are text-only: `draft` amber `#8A6D3B`, `confirmed` oxide, `shipped`
steel `#3F5A6B`, `done` green-grey `#4A6B52`. No pill badges, no dots.

## Type

- UI: **Instrument Sans** (already bundled). Neutral, holds up at 13-14px.
- Codes, SKUs, phone numbers, money: `ui-monospace, SFMono-Regular, Menlo, monospace`
  with `font-variant-numeric: tabular-nums`. These values get compared character by
  character; proportional digits make that slower.
- Sizes: 13px table body, 14px form controls, 20px page title. No 48px hero.

## Density

- Table row height 40px, padding 8px 12px, no card wrapper around the table.
- Section spacing is not uniform on purpose: 24px above a page title, 12px below it,
  32px between major blocks. Uniform gaps read as a template.
- Money right-aligned. Quantities right-aligned. Text left-aligned.

## Deliberately absent

Gradients, glass, glow, pill badges, decorative icons, fake metrics, dashboard charts,
skeleton screenshots, "trusted by" logos. None of them serve a person entering orders.

## Dials

ENERGY 2 · RHYTHM 3 · MOTION 1. Motion is limited to focus rings and hover states;
nothing animates on load.

## Status

Draft, written from inference rather than from a brief supplied by the operator. The
structure (routes, components, states) does not change if direction changes; only the
tokens in `resources/css/app.css` do.