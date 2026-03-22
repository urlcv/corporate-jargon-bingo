<?php

declare(strict_types=1);

namespace URLCV\CorporateJargonBingo\Laravel;

use App\Tools\Contracts\ToolInterface;

class CorporateJargonBingoTool implements ToolInterface
{
    public function slug(): string
    {
        return 'corporate-jargon-bingo';
    }

    public function name(): string
    {
        return 'Corporate Jargon Bingo';
    }

    public function summary(): string
    {
        return 'A 5×5 bingo card of classic workplace buzzwords and corporate jargon. Tick squares as you hear them in meetings — play in-browser or print a card.';
    }

    public function descriptionMd(): ?string
    {
        return <<<'MD'
## Corporate Jargon Bingo

Every meeting has *that* vocabulary — synergy, circle back, low-hanging fruit, bandwidth. This tool gives you a **5×5 bingo card** loaded with the classics. Listen to your next all-hands or read that strategy doc, and tick off squares as the jargon flies.

### How it works

- **New card** — Shuffle to get a fresh random 5×5 grid. Centre square is always FREE.
- **Tick squares** — Click/tap a square when you hear or read that phrase.
- **Line detection** — The tool automatically detects completed rows, columns, and diagonals.
- **Full house** — Mark all 24 jargon squares to win.
- **Tone selector** — Choose Mild (common meeting speak), Spicy (more eyeroll), or Mixed.
- **Shareable seed** — Share a URL with `?seed=…` to give someone the exact same card.
- **Print / PDF** — One-click print produces a clean, branded card for A4 or Letter paper.

### All in good fun

This is a light-hearted take on corporate culture — no targeting of individuals or employers. Just the shared experience of meeting-speak we all know.
MD;
    }

    public function categories(): array
    {
        return ['productivity'];
    }

    public function tags(): array
    {
        return ['corporate', 'jargon', 'bingo', 'meetings', 'humour', 'buzzwords'];
    }

    public function inputSchema(): array
    {
        return [];
    }

    public function run(array $input): array
    {
        return [];
    }

    public function mode(): string
    {
        return 'frontend';
    }

    public function isAsync(): bool
    {
        return false;
    }

    public function isPublic(): bool
    {
        return true;
    }

    public function frontendView(): ?string
    {
        return 'corporate-jargon-bingo::corporate-jargon-bingo';
    }

    public function rateLimitPerMinute(): int
    {
        return 30;
    }

    public function cacheTtlSeconds(): int
    {
        return 0;
    }

    public function sortWeight(): int
    {
        return 90;
    }
}
