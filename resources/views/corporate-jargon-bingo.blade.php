{{--
  Corporate Jargon Bingo — frontend-only Alpine.js tool.
  5×5 bingo card with corporate buzzwords. Centre = FREE.
  Tone presets: Mild / Mixed / Spicy. Shareable seed. Printable.
--}}
@push('head')
<style>
.jb-print-only { display: none; }

@media print {
    /*
     * #jargon-bingo-root lives inside <main>, not as body’s direct child — we cannot use
     * body > *:not(#jargon-bingo-root) or we hide the wrapper and print nothing.
     * Standard pattern: hide all descendants with visibility, then show only the tool.
     */
    body * {
        visibility: hidden;
    }
    #jargon-bingo-root,
    #jargon-bingo-root * {
        visibility: visible;
    }
    html, body {
        height: auto !important;
        overflow: visible !important;
    }
    #jargon-bingo-root {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        max-width: 100%;
        display: block !important;
        margin: 0 !important;
        padding: 0 !important;
        height: auto !important;
        max-height: none !important;
        overflow: visible !important;
    }
    /* Strip interactive chrome; display:none removes them from the print box */
    #jargon-bingo-root .jb-no-print {
        display: none !important;
    }

    .jb-print-only {
        display: block !important;
    }

    /*
     * overflow-x-auto makes overflow-y compute to auto (CSS), which clips the grid
     * vertically in print — only ~3 rows show. Force visible on the wrapper.
     */
    .jb-grid-outer {
        overflow: visible !important;
        max-height: none !important;
    }

    /* Clean up the grid for print — use auto rows, not 1fr (avoids squashing/clipping) */
    #jargon-bingo-grid {
        max-width: 380px !important;
        margin: 0 auto !important;
        border: 2px solid #1f2937 !important;
        border-radius: 0 !important;
        padding: 0 !important;
        gap: 0 !important;
        background: white !important;
        height: auto !important;
        max-height: none !important;
        overflow: visible !important;
        grid-template-rows: auto repeat(5, auto) !important;
        align-content: start !important;
    }

    #jargon-bingo-grid [role="columnheader"] {
        border-bottom: 2px solid #1f2937;
        font-size: 13px;
        padding: 4px 0;
    }

    #jargon-bingo-grid [role="gridcell"] {
        border: 1px solid #9ca3af !important;
        border-radius: 0 !important;
        min-height: 0 !important;
        padding: 6px 4px !important;
        font-size: 9px !important;
        line-height: 1.2 !important;
        background: white !important;
        color: #1f2937 !important;
        box-shadow: none !important;
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }

    #jargon-bingo-grid [role="gridcell"][aria-pressed="true"] {
        background: #e5e7eb !important;
    }

    #jargon-bingo-grid .jb-free-cell {
        background: #f3f4f6 !important;
    }

    @page {
        size: auto;
        margin: 15mm;
    }
}
</style>
@endpush

<div
    x-data="corporateJargonBingo()"
    x-init="init()"
    x-cloak
    id="jargon-bingo-root"
    class="space-y-6"
>
    {{-- Screen-only intro --}}
    <p class="text-sm text-gray-600 jb-no-print">
        Tick each square when you hear or read the phrase in a meeting, email, or strategy doc. Get a line or full house to win.
    </p>

    {{-- Tone selector (screen only) --}}
    <div class="flex flex-wrap items-center gap-2 jb-no-print">
        <span class="text-sm font-medium text-gray-700">Tone:</span>
        <template x-for="opt in ['mild', 'mixed', 'spicy']" :key="opt">
            <button
                type="button"
                @click="setTone(opt)"
                :class="[
                    'px-3 py-1.5 text-sm font-medium rounded-lg border transition-colors capitalize',
                    tone === opt
                        ? 'bg-primary-600 text-white border-primary-600'
                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                ]"
                x-text="opt"
            ></button>
        </template>
    </div>

    {{-- Actions (screen only) --}}
    <div class="flex flex-wrap items-center gap-3 jb-no-print">
        <button
            type="button"
            @click="shuffleCard()"
            class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors"
        >
            New card
        </button>
        <button
            type="button"
            @click="clearTicks()"
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors"
        >
            Reset ticks
        </button>
        <button
            type="button"
            @click="window.print()"
            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print card
        </button>
        <button
            type="button"
            @click="copySummary()"
            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
            </svg>
            <span x-text="copied ? 'Copied!' : 'Copy summary'"></span>
        </button>
    </div>

    {{-- Seed display (screen only) --}}
    <div x-show="seed" class="text-xs text-gray-400 jb-no-print">
        Seed: <span x-text="seed" class="font-mono"></span>
    </div>

    {{-- Print-only header: URLCV branding + title --}}
    <div class="jb-print-only text-center space-y-2 mb-4" aria-hidden="true">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 180 36" style="height: 28px; width: auto; margin: 0 auto;">
            <rect x="2" y="4" width="24" height="24" rx="6" fill="#0EA5E9"/>
            <rect x="8" y="10" width="13" height="3" rx="1" fill="#ffffff" opacity="0.95"/>
            <rect x="8" y="15" width="9" height="3" rx="1" fill="#ffffff" opacity="0.95"/>
            <rect x="8" y="20" width="11" height="3" rx="1" fill="#ffffff" opacity="0.95"/>
            <text x="36" y="24" font-family="Inter, system-ui, sans-serif" font-size="20" font-weight="700" fill="#0F172A" letter-spacing="-0.25">urlcv</text>
        </svg>
        <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0;">Corporate Jargon Bingo</h2>
        <p style="font-size: 11px; color: #6b7280; margin: 0;">Tick each square when you hear or read it. Centre = FREE.</p>
        <p x-show="seed" style="font-size: 10px; color: #9ca3af; margin: 0;">Seed: <span x-text="seed"></span></p>
    </div>

    {{-- Bingo grid 5×5 — used for BOTH screen and print --}}
    <div class="jb-grid-outer overflow-x-auto flex justify-center">
        <div
            id="jargon-bingo-grid"
            class="w-full max-w-lg border-2 border-gray-300 rounded-lg p-2 sm:p-3 bg-white"
            style="display: grid; grid-template-columns: repeat(5, 1fr); grid-template-rows: auto repeat(5, 1fr); gap: 6px;"
            role="grid"
            aria-label="Corporate Jargon Bingo card"
        >
            {{-- Column headers B-I-N-G-O --}}
            <div class="flex items-center justify-center text-sm font-bold text-gray-700" role="columnheader">B</div>
            <div class="flex items-center justify-center text-sm font-bold text-gray-700" role="columnheader">I</div>
            <div class="flex items-center justify-center text-sm font-bold text-gray-700" role="columnheader">N</div>
            <div class="flex items-center justify-center text-sm font-bold text-gray-700" role="columnheader">G</div>
            <div class="flex items-center justify-center text-sm font-bold text-gray-700" role="columnheader">O</div>
            {{-- 25 cells --}}
            <template x-for="(row, i) in grid" :key="i">
                <template x-for="(cell, j) in row" :key="j">
                    <button
                        type="button"
                        @click="toggle(i, j)"
                        :aria-pressed="cell.ticked"
                        :aria-label="cell.free ? 'Free space, always marked' : cell.label + (cell.ticked ? ', marked' : '')"
                        role="gridcell"
                        :class="[
                            'flex items-center justify-center p-1.5 sm:p-2 text-center text-[10px] sm:text-xs font-medium rounded border-2 transition-colors min-h-[3rem] sm:min-h-[3.5rem] focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1',
                            cell.free
                                ? 'bg-amber-50 border-amber-400 text-amber-800 font-bold cursor-default jb-free-cell'
                                : cell.ticked
                                    ? 'bg-primary-100 border-primary-500 text-primary-800'
                                    : 'bg-white border-gray-300 text-gray-800 hover:border-gray-400 hover:bg-gray-50 cursor-pointer'
                        ]"
                    >
                        <span class="flex items-center gap-1">
                            <template x-if="cell.ticked && !cell.free">
                                <svg class="h-3 w-3 sm:h-3.5 sm:w-3.5 flex-shrink-0 text-primary-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </template>
                            <span x-text="cell.free ? '⭐ FREE' : cell.label"></span>
                        </span>
                    </button>
                </template>
            </template>
        </div>
    </div>

    {{-- Print-only footer --}}
    <p class="jb-print-only text-center" style="font-size: 10px; color: #9ca3af; margin-top: 12px;">urlcv.com/tools/corporate-jargon-bingo</p>

    {{-- Progress panel (screen only) --}}
    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-3 jb-no-print">
        <div class="flex flex-wrap items-center gap-4 text-sm">
            <span class="text-gray-600">
                Marked: <span class="font-semibold text-gray-900" x-text="tickCount"></span> / 24
            </span>
            <span class="text-gray-600">
                Lines: <span class="font-semibold text-gray-900" x-text="lineCount"></span> / 12
            </span>
        </div>
        <div x-show="lineCount > 0 && !hasFullHouse" x-transition class="flex items-center gap-2 text-green-700 font-medium text-sm">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>Line! <span x-show="lineCount > 1" x-text="'(' + lineCount + ' lines)'"></span></span>
        </div>
        <div x-show="hasFullHouse" x-transition class="flex items-center gap-2 text-primary-700 font-bold text-sm">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>Full house! All 24 squares marked.</span>
        </div>
    </div>

    {{-- Disclaimer (screen only) --}}
    <p class="text-xs text-gray-400 italic jb-no-print">
        Just for fun — not targeting any individual, employer, or organisation.
    </p>

    {{-- Tip (screen only) --}}
    <div class="rounded-xl p-4 text-sm bg-blue-50 border border-blue-200 text-blue-800 jb-no-print">
        <span class="font-semibold">Tip:</span> Use <strong>Print card</strong> to save a clean PDF before your next meeting. Share a specific card layout by copying the page URL — the <code class="text-xs bg-blue-100 px-1 rounded">?seed=</code> parameter reproduces the exact same card. Switch between <strong>Mild</strong>, <strong>Mixed</strong>, and <strong>Spicy</strong> tones for different levels of corporate cringe.
    </div>
</div>

@push('scripts')
<script>
function corporateJargonBingo() {
    const MILD = [
        "Synergy", "Circle back", "Touch base", "Bandwidth", "Alignment",
        "Action items", "Stakeholders", "Deliverables", "Going forward",
        "Best practice", "On the same page", "Moving the needle",
        "End of day", "Reach out", "Follow up", "Take offline",
        "Key takeaways", "Deep dive", "High level", "Roadmap",
        "Pipeline", "Onboarding", "Scalable", "Streamline",
        "Leverage", "Ecosystem", "Value-add", "Drill down",
        "Loop in", "Ping me", "Cadence", "Touchpoint",
        "Visibility", "Heads up", "FYI", "Per my last email",
        "As per", "Noted", "Align on this", "Close the loop",
        "On my radar", "Keep me posted", "Raise the bar",
        "Cross-functional", "Collaborate", "Team effort",
        "Feedback loop", "Best-in-class", "Core competency",
        "Mission-critical"
    ];

    const SPICY = [
        "Low-hanging fruit", "Boil the ocean", "Move the goalposts",
        "Thought leader", "Disrupt", "Paradigm shift", "Blue-sky thinking",
        "Lean in", "Growth hacking", "10x", "Ideation",
        "Unpack that", "Net-net", "Peel the onion", "Bleeding edge",
        "Pivot", "North star", "Circle the wagons", "Drink the Kool-Aid",
        "Open the kimono", "Synergise", "Hyperscale", "Rockstar",
        "Ninja", "Guru", "Evangelist", "Dogfooding",
        "Run it up the flagpole", "Think outside the box",
        "Hit the ground running", "Win-win", "Game changer",
        "Balls in the air", "Ducks in a row", "Helicopter view",
        "Swim lane", "Tiger team", "War room", "Sanity check",
        "Herding cats", "Sharpen the saw", "Eat your own dog food",
        "Double-click on that", "Take this to the next level",
        "Put a pin in it", "Bite the bullet", "Move the cheese",
        "Solutioneering", "Rightsizing", "Future-proof"
    ];

    function seededRng(seed) {
        let h = 0;
        for (let i = 0; i < seed.length; i++) {
            h = ((h << 5) - h + seed.charCodeAt(i)) | 0;
        }
        let s = h >>> 0;
        return function() {
            s ^= s << 13;
            s ^= s >> 17;
            s ^= s << 5;
            return (s >>> 0) / 4294967296;
        };
    }

    function shuffleWithRng(arr, rng) {
        const out = [...arr];
        for (let i = out.length - 1; i > 0; i--) {
            const j = Math.floor(rng() * (i + 1));
            [out[i], out[j]] = [out[j], out[i]];
        }
        return out;
    }

    function generateSeed() {
        const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        let s = '';
        for (let i = 0; i < 8; i++) s += chars[Math.floor(Math.random() * chars.length)];
        return s;
    }

    return {
        grid: [],
        tone: 'mixed',
        seed: '',
        copied: false,

        init() {
            const params = new URLSearchParams(window.location.search);
            const urlSeed = params.get('seed');
            const urlTone = params.get('tone');

            if (urlTone && ['mild', 'mixed', 'spicy'].includes(urlTone)) {
                this.tone = urlTone;
            }

            if (urlSeed) {
                this.seed = urlSeed;
            } else {
                this.seed = generateSeed();
            }

            this.buildCard();
        },

        getPool() {
            if (this.tone === 'mild') return [...MILD];
            if (this.tone === 'spicy') return [...SPICY];
            return [...MILD, ...SPICY];
        },

        buildCard() {
            const rng = seededRng(this.seed + ':' + this.tone);
            const pool = this.getPool();
            const shuffled = shuffleWithRng(pool, rng);
            const grid = [];
            let idx = 0;
            for (let i = 0; i < 5; i++) {
                const row = [];
                for (let j = 0; j < 5; j++) {
                    const isCenter = i === 2 && j === 2;
                    row.push({
                        label: isCenter ? 'FREE' : shuffled[idx++],
                        free: isCenter,
                        ticked: isCenter,
                    });
                }
                grid.push(row);
            }
            this.grid = grid;
        },

        shuffleCard() {
            this.seed = generateSeed();
            this.buildCard();
            this.updateUrl();
        },

        setTone(t) {
            this.tone = t;
            this.seed = generateSeed();
            this.buildCard();
            this.updateUrl();
        },

        updateUrl() {
            const url = new URL(window.location);
            url.searchParams.set('seed', this.seed);
            url.searchParams.set('tone', this.tone);
            window.history.replaceState({}, '', url);
        },

        clearTicks() {
            for (const row of this.grid) {
                for (const cell of row) {
                    cell.ticked = cell.free;
                }
            }
        },

        toggle(i, j) {
            const cell = this.grid[i][j];
            if (cell.free) return;
            cell.ticked = !cell.ticked;
        },

        get tickCount() {
            let n = 0;
            for (const row of this.grid) {
                for (const cell of row) {
                    if (cell.ticked && !cell.free) n++;
                }
            }
            return n;
        },

        get lineCount() {
            const g = this.grid;
            if (!g.length) return 0;
            let count = 0;
            for (let i = 0; i < 5; i++) {
                if (g[i].every(c => c.ticked)) count++;
            }
            for (let j = 0; j < 5; j++) {
                if (g.every(row => row[j].ticked)) count++;
            }
            if (g[0][0].ticked && g[1][1].ticked && g[2][2].ticked && g[3][3].ticked && g[4][4].ticked) count++;
            if (g[0][4].ticked && g[1][3].ticked && g[2][2].ticked && g[3][1].ticked && g[4][0].ticked) count++;
            return count;
        },

        get hasFullHouse() {
            return this.grid.length > 0 && this.grid.every(row => row.every(c => c.ticked));
        },

        copySummary() {
            const url = window.location.origin + '/tools/corporate-jargon-bingo';
            let msg;
            if (this.hasFullHouse) {
                msg = `Full house on Corporate Jargon Bingo! — ${url}`;
            } else if (this.lineCount > 0) {
                msg = `${this.lineCount} line${this.lineCount > 1 ? 's' : ''} on Corporate Jargon Bingo (${this.tickCount}/24 marked) — ${url}`;
            } else {
                msg = `${this.tickCount}/24 marked on Corporate Jargon Bingo — ${url}`;
            }
            navigator.clipboard.writeText(msg).then(() => {
                this.copied = true;
                setTimeout(() => { this.copied = false; }, 2000);
            });
        },
    };
}
</script>
@endpush
