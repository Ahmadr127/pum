/**
 * scanpdf.js — PDF scan & parser untuk PUM system
 *
 * Format PDF target (Detail Pengajuan):
 *   Detail Pengajuan
 *   00440/ADV/PNJ/02/2026
 *
 *   Tanggal Pengajuan    18 Feb 2026
 *   Nama Pengaju         RIA FAJARROHMI, SE [-745]
 *   Jumlah Pengajuan     6.000.000
 *   Keterangan           test
 *   Oleh Petugas         RIA FAJARROHMI, SE
 *
 * Strategi ekstraksi (urutan prioritas):
 *   A. Same-row table: label + value pada Y yang sama (kolom kiri & kanan)
 *   B. Next-line: label di baris N, nilai di N+1
 *   C. Colon-inline: "Label : Nilai" pada satu baris
 *   D. Standalone pattern: date/amount/code tanpa label
 */

/* global pdfjsLib, Alpine */

function pdfScanner() {
    return {
        open: false,
        state: 'idle',   // 'idle' | 'loading'
        errorMsg: '',

        reset() {
            this.state = 'idle';
            this.errorMsg = '';
            const inp = document.getElementById('pdfFileInput');
            if (inp) inp.value = '';
        },

        handleFileChange(e) {
            const f = e.target.files[0];
            if (f) this.processFile(f);
        },

        handleDrop(e) {
            const f = e.dataTransfer.files[0];
            if (f && f.type === 'application/pdf') this.processFile(f);
            else this.errorMsg = 'Hanya file PDF yang diterima.';
        },

        // ──────────────────────────────────────────────────────────────────
        //  MAIN: load PDF, extract text items with coordinates, parse
        // ──────────────────────────────────────────────────────────────────
        async processFile(file) {
            if (file.size > 10 * 1024 * 1024) {
                this.errorMsg = 'Ukuran file melebihi 10 MB.'; return;
            }
            this.state = 'loading';
            this.errorMsg = '';

            try {
                await this._waitForPdfJs();

                const pdf = await pdfjsLib.getDocument({
                    data: await file.arrayBuffer()
                }).promise;

                /* Collect ALL text items with real x,y positions */
                const items = [];
                for (let p = 1; p <= pdf.numPages; p++) {
                    const page = await pdf.getPage(p);
                    const content = await page.getTextContent();
                    for (const it of content.items) {
                        const s = it.str;
                        if (s && s.trim()) {
                            items.push({
                                str: s,
                                x: it.transform[4],
                                y: it.transform[5],
                                w: it.width || 0,   // actual measured width if available
                            });
                        }
                    }
                }

                /* Sort: top → bottom (y DESC), left → right (x ASC) */
                items.sort((a, b) => (b.y - a.y) || (a.x - b.x));

                /* Group items into visual rows (Y within ±6pt = same row).
                   A tolerance of 6 handles slight baseline shifts in PDFs. */
                const rows = [];
                let cur = null;
                for (const it of items) {
                    if (!cur || Math.abs(it.y - cur.y) > 6) {
                        cur = { y: it.y, cells: [] };
                        rows.push(cur);
                    }
                    cur.cells.push(it);
                }

                /*
                 * Build two parallel arrays:
                 *   lines[]   — full row text (cells joined, "|" at column gaps ≥15pt)
                 *   rowCells[] — raw cells for that row (for two-column lookup)
                 */
                const lines = [];
                const rowCells = [];
                for (const row of rows) {
                    let line = '';
                    let prevEnd = null;
                    for (const cell of row.cells) {
                        /* End position of previous cell:
                           prefer measured width; fall back to char-count heuristic */
                        if (prevEnd !== null && cell.x - prevEnd > 15) {
                            line += ' | ';
                        }
                        line += cell.str;
                        prevEnd = cell.x + (cell.w > 0 ? cell.w : cell.str.length * 5.5);
                    }
                    line = line.trim();
                    if (line) {
                        lines.push(line);
                        rowCells.push(row.cells);
                    }
                }

                console.log('[ScanPDF] Lines ↓\n' + lines.join('\n'));

                const parsed = PumPdfParser.parse(lines, rowCells);
                console.log('[ScanPDF] Result:', parsed);

                this.open = false;
                this.$dispatch('scan-result', { ...parsed, scannedFile: file });

            } catch (err) {
                console.error('[ScanPDF] Error:', err);
                this.errorMsg = 'Gagal membaca PDF. Pastikan file tidak terenkripsi.';
                this.state = 'idle';
            }
        },

        _waitForPdfJs() {
            return new Promise(resolve => {
                const check = () => {
                    if (typeof pdfjsLib !== 'undefined') {
                        pdfjsLib.GlobalWorkerOptions.workerSrc =
                            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                        resolve();
                    } else setTimeout(check, 100);
                };
                check();
            });
        },
    };
}


/* ════════════════════════════════════════════════════════════════════════
 *  PumPdfParser — pure parsing logic (no DOM / Alpine dependency)
 * ════════════════════════════════════════════════════════════════════════ */
const PumPdfParser = {

    /* Label synonym groups — each field has many possible label names */
    LABELS: {
        no_surat: [
            /no\.?\s*surat/i, /nomor\s*surat/i,
            /kode\s*(?:surat|referensi|dokumen)?/i,
            /nomor\s*(?:referensi|dokumen)?/i,
            /no\s*ref/i, /ref(?:erensi)?/i,
            /no\.?\s*dokumen/i,
        ],
        request_date: [
            /tanggal\s+pengajuan/i, /tgl\.?\s*pengajuan/i,
            /tanggal\s+permohonan/i, /tgl\.?\s*permohonan/i,
            /tanggal\s+surat/i,
            /tanggal/i, /tgl\.?(?!\s*\d)/i,   // "tgl" but not "tgl.02"
            /date/i,
        ],
        requester_name: [
            /nama\s+pengaju/i, /nama\s+pemohon/i,
            /nama\s+karyawan/i, /nama\s+pegawai/i,
            /nama\s+penanggung\s*jawab/i,
            /pengaju/i, /pemohon/i,
            /nama/i,
            /requestor/i,
        ],
        amount: [
            /jumlah\s+pengajuan/i, /jumlah\s+yang\s+diajukan/i,
            /total\s+pengajuan/i, /jumlah\s+uang/i,
            /jumlah\s+dana/i, /jumlah\s+biaya/i,
            /jumlah/i, /nominal/i, /total\s+biaya/i,
            /nilai/i, /besaran/i, /amount/i,
        ],
        description: [
            /keterangan\s+pengajuan/i, /keperluan\s+pengajuan/i,
            /keterangan\s+penggunaan/i, /tujuan\s+penggunaan/i,
            /keterangan/i, /keperluan/i, /tujuan/i,
            /uraian/i, /deskripsi/i, /kebutuhan/i,
            /description/i, /purpose/i,
        ],
    },

    /* Month name → "MM" */
    MON: {
        januari: '01', jan: '01', february: '02', februari: '02', feb: '02',
        maret: '03', mar: '03', april: '04', apr: '04',
        mei: '05', may: '05', juni: '06', jun: '06',
        juli: '07', jul: '07', agustus: '08', agu: '08', aug: '08',
        september: '09', sep: '09', oktober: '10', okt: '10', oct: '10',
        november: '11', nov: '11', desember: '12', des: '12', dec: '12',
    },

    // ── Entry point ───────────────────────────────────────────────────────
    parse(lines, rowCells) {
        const result = {
            no_surat: '', request_date: '', requester_name: '',
            amount: '', description: '',
        };

        const allPat = Object.values(this.LABELS).flat();

        /* Helper: is line a junk/header we skip as a value candidate? */
        const isJunk = (line) =>
            line.length < 2 ||
            /detail\s+pengajuan|permintaan\s+uang\s+muka|approval\s+log/i.test(line);

        /* Helper: does `line` contain any known label? */
        const isAnyLabel = (line) => allPat.some(p => p.test(line));

        /* Helper: next 1..n non-empty, non-junk lines that are not labels */
        const nextValues = (i, n = 5) => {
            const out = [];
            for (let j = i + 1; out.length < n && j < lines.length; j++) {
                const l = lines[j].trim();
                if (l && !isJunk(l)) out.push(l);
            }
            return out;
        };

        /* ── Strategy A: same-row two-column lookup ─────────────────────
         *  The PDF table has label in left column and value in right column.
         *  After row-grouping + "|" insertion the line looks like:
         *    "Tanggal Pengajuan | 18 Feb 2026"
         *  We try label patterns against the LEFT part and take RIGHT part. */
        const extractTwoCol = (labelPats, postFn) => {
            for (const line of lines) {
                if (!line.includes('|')) continue;
                const parts = line.split('|').map(s => s.trim());
                const left = parts[0];
                const right = parts.slice(1).join(' ').trim();
                if (!right) continue;
                if (labelPats.some(p => p.test(left))) {
                    return postFn ? postFn(right) : right;
                }
            }
            return null;
        };

        /* ── Strategy B: inline colon — "Label : Value" on one line ─── */
        const extractInline = (labelPats, postFn) => {
            for (const line of lines) {
                for (const p of labelPats) {
                    const re = new RegExp(p.source + '\\s*[:\\-]\\s*(.+)', 'i');
                    const m = line.match(re);
                    if (m && m[1] && m[1].trim()) {
                        return postFn ? postFn(m[1].trim()) : m[1].trim();
                    }
                }
            }
            return null;
        };

        /* ── Strategy C: label on its own line → value on next line ─── */
        const extractNextLine = (labelPats, postFn) => {
            for (let i = 0; i < lines.length; i++) {
                const line = lines[i];
                // Whole line matches label (not a colon-style line)
                if (labelPats.some(p => p.test(line)) && !line.includes(':')) {
                    for (const next of nextValues(i, 5)) {
                        if (!isAnyLabel(next)) {
                            return postFn ? postFn(next) : next;
                        }
                    }
                }
            }
            return null;
        };

        /* ── Combined extractor: A → B → C ─────────────────────────── */
        const extract = (fieldKey, postFn) => {
            const pats = this.LABELS[fieldKey];
            return extractTwoCol(pats, postFn)
                ?? extractInline(pats, postFn)
                ?? extractNextLine(pats, postFn)
                ?? '';
        };

        // ── Field 1: No. Surat ─────────────────────────────────────────
        /* Try standalone code patterns first (they're very distinctive) */
        const CODE_RES = [
            /\b(\d{2,6}\/[A-Z]{2,}(?:\/[A-Z0-9]+)+)\b/,        // 00440/ADV/PNJ/02/2026
            /\b([A-Z]{2,}(?:\/[A-Z0-9]+){2,})\b/,               // ADV/PNJ/02/2026
            /\b(\d{2,6}[-][A-Z]{2,}(?:[-][A-Z0-9]+)+)\b/,       // dashed variant
        ];
        for (const line of lines) {
            let hit = false;
            for (const re of CODE_RES) {
                const m = line.match(re);
                if (m) { result.no_surat = m[1]; hit = true; break; }
            }
            if (hit) break;
        }
        if (!result.no_surat) {
            result.no_surat = extract('no_surat', v =>
                v.replace(/\s+\d{1,2}\s+[A-Za-z]+\s+\d{4}.*$/, '').trim()
            );
        }

        // ── Field 2: Tanggal Pengajuan ─────────────────────────────────
        const parseDate = (raw) => {
            if (!raw) return '';
            const s = raw.replace(/^[:\-\s]+/, '').trim();
            // pick out any date-like substring
            const dm = s.match(
                /(\d{1,2}\s+[A-Za-z]+\.?\s+\d{4}|(?:\d{4}|\d{2})[-\/]\d{2}[-\/]\d{4}|\d{1,2}[-\/]\d{1,2}[-\/]\d{4})/
            );
            return this.parseDate(dm ? dm[1] : s);
        };

        result.request_date = extract('request_date', parseDate);

        /* Fallback: scan every line for a recognisable date */
        if (!result.request_date) {
            const DRE = /\b(\d{1,2}\s+(?:Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember|Jan|Feb|Mar|Apr|Jun|Jul|Agu|Aug|Sep|Okt|Oct|Nov|Des)[a-z]*\.?\s+\d{4})\b/i;
            for (const line of lines) {
                const m = line.match(DRE);
                if (m) { result.request_date = this.parseDate(m[1]); break; }
            }
        }
        if (!result.request_date) {
            for (const line of lines) {
                const m = line.match(/\b(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4})\b/);
                if (m) { result.request_date = this.parseDate(m[1]); break; }
            }
        }

        // ── Field 3: Nama Pengaju ──────────────────────────────────────
        const cleanName = (v) =>
            v.replace(/^[:\-\s]+/, '')
                .replace(/\s*\[.*?\]/g, '')           // strip [-745] or [admin]
                .replace(/,\s*(?:SE|ST|SH|SKM|SKep|Ns|MM|Mkes|MBA|SPd|SIP|Ak|S\.Sos)\.?\s*$/gi, '')
                .trim();

        result.requester_name = extract('requester_name', cleanName);

        // Discard if it looks like a code or is too short
        if (!result.requester_name ||
            /^\d+$/.test(result.requester_name) ||
            /\d{3,}\//.test(result.requester_name) ||
            result.requester_name.length < 2) {
            result.requester_name = '';
        }

        // ── Field 4: Jumlah / Nominal ──────────────────────────────────
        const cleanAmount = (v) => {
            const s = v.replace(/^[:\-\s]+/, '');
            /* Handle Indonesian "6.000.000" (dots as thousands separator)
               AND Western "6,000,000" AND plain "6000000"
               AND "Rp 6.000.000" */
            const m = s.match(/(?:Rp\.?\s*)?([\d.,]+)/i);
            if (!m) return s.replace(/\D/g, '');
            let num = m[1];
            /* If dots appear as thousand-sep (e.g. "6.000.000") but NO comma: remove dots */
            if (num.includes('.') && !num.includes(',')) num = num.replace(/\./g, '');
            /* If commas are thousand-sep (e.g. "6,000,000"): remove commas */
            else num = num.replace(/,/g, '');
            return num.replace(/\D/g, '');
        };

        result.amount = extract('amount', cleanAmount);

        if (!result.amount) {
            for (const line of lines) {
                const m = line.match(/Rp\.?\s*([\d.,]+)/i);
                if (m) { result.amount = cleanAmount(m[1]); break; }
            }
        }

        // ── Field 5: Keterangan / Keperluan ───────────────────────────
        const cleanDesc = (v) => v.replace(/^[:\-\s]+/, '').trim();
        result.description = extract('description', cleanDesc);
        if (isAnyLabel(result.description)) result.description = '';

        return result;
    },

    // ── Date parser ───────────────────────────────────────────────────────
    parseDate(str) {
        const M = this.MON;
        const s = (str || '').trim();

        // "18 Februari 2026" / "18 Feb 2026" / "18 Feb. 2026"
        let m = s.match(/(\d{1,2})\s+([A-Za-z]+)\.?\s+(\d{4})/);
        if (m) {
            const day = m[1].padStart(2, '0');
            const key = m[2].toLowerCase();
            const mon = M[key] || M[key.slice(0, 3)] || '01';
            return `${m[3]}-${mon}-${day}`;
        }
        // "18/02/2026" or "18-02-2026"
        m = s.match(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/);
        if (m) return `${m[3]}-${m[2].padStart(2, '0')}-${m[1].padStart(2, '0')}`;
        // ISO: "2026-02-18"
        if (/^\d{4}-\d{2}-\d{2}$/.test(s)) return s;
        return '';
    },
};
