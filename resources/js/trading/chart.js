/**
 * ═══════════════════════════════════════════════════════════════
 *  RACKSEPHNOX · KLineChart Wrapper
 *  Bundled via Vite · No CDN · Offline-capable · 888 Hz
 * ═══════════════════════════════════════════════════════════════
 *
 *  Exposes: window.RackChart
 *    .init(element, options)         → creates chart instance
 *    .dispose(element)               → destroys
 *    .applyData(element, candles)    → replaces all data
 *    .addIndicator(el, name, params) → adds indicator
 *    .removeIndicator(el, name)      → removes indicator
 *    .resize(el, w, h)               → resizes
 *    .screenshot(el)                 → returns PNG data URL
 *    .version                        → klinecharts version
 */

import * as klinecharts from 'klinecharts';

// ─── Chart registry — supports multiple charts ───
const REGISTRY = new Map();

// ─── Golden theme ───
const THEME = {
    grid: {
        show: true,
        horizontal: { color: 'rgba(51,65,85,0.35)', size: 1, style: 'dashed' },
        vertical:   { color: 'rgba(51,65,85,0.35)', size: 1, style: 'dashed' },
    },
    candle: {
        bar: {
            upColor:          '#26A69A',
            downColor:        '#EF5350',
            upBorderColor:    '#26A69A',
            downBorderColor:  '#EF5350',
            upWickColor:      '#26A69A',
            downWickColor:    '#EF5350',
        },
        area: {
            lineColor:        '#D4AF37',
            lineSize:         2,
            backgroundColor:  'rgba(212,175,55,0.15)',
        },
        priceMark: {
            high: { show: true, color: '#D4AF37', text: { color: '#0F172A', size: 11 } },
            low:  { show: true, color: '#D4AF37', text: { color: '#0F172A', size: 11 } },
            last: {
                show: true,
                upColor:   '#26A69A',
                downColor: '#EF5350',
                noChangeColor: '#D4AF37',
                text: { color: '#0F172A', size: 11, family: 'Inter, sans-serif' },
            },
        },
        tooltip: {
            showRule: 'follow_cross',
            showType: 'standard',
            offsetLeft: 8,
            offsetTop: 8,
            text: { color: '#D4AF37', size: 11, family: 'Inter, sans-serif' },
        },
    },
    indicator: {
        ohlc: {
            upColor: '#26A69A',
            downColor: '#EF5350',
            noChangeColor: '#D4AF37',
        },
        lines: [
            { color: '#D4AF37', size: 1 },
            { color: '#26A69A', size: 1 },
            { color: '#EF5350', size: 1 },
            { color: '#7E57C2', size: 1 },
            { color: '#42A5F5', size: 1 },
            { color: '#FFB74D', size: 1 },
        ],
        bars: [
            { upColor: '#26A69A', downColor: '#EF5350', noChangeColor: '#D4AF37' },
        ],
    },
    xAxis: {
        show: true,
        axisLine: { show: true, color: 'rgba(212,175,55,0.3)', size: 1 },
        tickLine: { show: true, color: 'rgba(212,175,55,0.3)', size: 1 },
        tickText: { show: true, color: '#D4AF37', size: 11, family: 'Inter, sans-serif' },
    },
    yAxis: {
        show: true,
        axisLine: { show: true, color: 'rgba(212,175,55,0.3)', size: 1 },
        tickLine: { show: true, color: 'rgba(212,175,55,0.3)', size: 1 },
        tickText: { show: true, color: '#D4AF37', size: 11, family: 'Inter, sans-serif' },
    },
    separator: { size: 2, color: 'rgba(212,175,55,0.2)' },
    crosshair: {
        show: true,
        horizontal: {
            show: true,
            line: { show: true, style: 'dashed', dashedValue: [4, 4], size: 1, color: '#D4AF37' },
            text: { show: true, style: 'fill', color: '#0F172A', size: 11, paddingLeft: 4, paddingRight: 4, paddingTop: 2, paddingBottom: 2, borderStyle: 'solid', borderSize: 1, borderColor: '#D4AF37', backgroundColor: '#D4AF37' },
        },
        vertical: {
            show: true,
            line: { show: true, style: 'dashed', dashedValue: [4, 4], size: 1, color: '#D4AF37' },
            text: { show: true, style: 'fill', color: '#0F172A', size: 11, paddingLeft: 4, paddingRight: 4, paddingTop: 2, paddingBottom: 2, borderStyle: 'solid', borderSize: 1, borderColor: '#D4AF37', backgroundColor: '#D4AF37' },
        },
    },
    overlay: {
        line: { color: '#D4AF37', size: 1, style: 'solid', dashedValue: [2, 2] },
        text: { color: '#D4AF37', size: 12, family: 'Inter, sans-serif' },
    },
};

// ─── Default indicator parameters ───
const DEFAULT_PARAMS = {
    MA:    [{ calcParams: [5, 10, 30, 60] }],
    EMA:   [{ calcParams: [6, 12, 20] }],
    BOLL:  [{ calcParams: [20, 2] }],
    MACD:  [{ calcParams: [12, 26, 9] }],
    RSI:   [{ calcParams: [6, 12, 24] }],
    KDJ:   [{ calcParams: [9, 3, 3] }],
    OBV:   [{ calcParams: [30] }],
    VOL:   [{ calcParams: [] }],
};

// ─── Public API ───
const RackChart = {
    version: (klinecharts.version || 'unknown'),

    /**
     * Initialize a chart inside `element`.
     * Returns the chart instance (also cached internally).
     */
    init(element, options = {}) {
        if (!element) {
            console.error('[RackChart] init — element is null');
            return null;
        }

        // Dispose existing if any
        if (REGISTRY.has(element)) {
            try { klinecharts.dispose(element); } catch (e) {}
            REGISTRY.delete(element);
        }

        const chart = klinecharts.init(element, {
            styles: options.styles || THEME,
            timezone: options.timezone || 'Africa/Nairobi',
            locale: options.locale || 'en-US',
            layout: options.layout || [
                { type: 'candle', content: ['candle', 'MA', 'EMA', 'BOLL'], options: { id: 'candle_pane' } },
                { type: 'indicator', content: ['VOL'], options: { id: 'vol_pane', height: 80 } },
            ],
        });

        REGISTRY.set(element, chart);
        return chart;
    },

    /** Get chart instance for an element */
    get(element) {
        return REGISTRY.get(element) || null;
    },

    /** Dispose chart for an element */
    dispose(element) {
        if (!element) return;
        if (REGISTRY.has(element)) {
            try { klinecharts.dispose(element); } catch (e) {}
            REGISTRY.delete(element);
        }
    },

    /** Replace all candle data */
    applyData(element, candles) {
        const chart = REGISTRY.get(element);
        if (!chart) return false;
        try {
            chart.applyNewData(candles);
            return true;
        } catch (e) {
            console.error('[RackChart] applyData failed', e);
            return false;
        }
    },

    /** Append a single candle (real-time update) */
    updateData(element, candle) {
        const chart = REGISTRY.get(element);
        if (!chart) return false;
        try {
            chart.updateData(candle);
            return true;
        } catch (e) {
            console.error('[RackChart] updateData failed', e);
            return false;
        }
    },

    /** Add indicator to the candle pane */
    addIndicator(element, name, params = null) {
        const chart = REGISTRY.get(element);
        if (!chart) return null;
        try {
            const p = params || DEFAULT_PARAMS[name] || [{}];
            const id = chart.createIndicator(name, false, { id: 'candle_pane' }, p);
            return id;
        } catch (e) {
            console.warn('[RackChart] addIndicator failed', name, e.message);
            return null;
        }
    },

    /** Add indicator to a specific pane */
    addIndicatorToPane(element, name, paneId, params = null) {
        const chart = REGISTRY.get(element);
        if (!chart) return null;
        try {
            const p = params || DEFAULT_PARAMS[name] || [{}];
            return chart.createIndicator(name, false, { id: paneId }, p);
        } catch (e) {
            return null;
        }
    },

    /** Remove indicator */
    removeIndicator(element, paneId, indicatorId) {
        const chart = REGISTRY.get(element);
        if (!chart) return false;
        try {
            chart.removeIndicator(paneId, indicatorId);
            return true;
        } catch (e) {
            return false;
        }
    },

    /** Resize the chart */
    resize(element, width = null, height = null) {
        const chart = REGISTRY.get(element);
        if (!chart) return false;
        try {
            const w = width ?? element.clientWidth;
            const h = height ?? element.clientHeight;
            chart.resize(w, h);
            return true;
        } catch (e) {
            return false;
        }
    },

    /** Export current chart as PNG data URL */
    screenshot(element) {
        const chart = REGISTRY.get(element);
        if (!chart) return null;
        try {
            return chart.getConvertPictureUrl(true, 'png', '#0F172A');
        } catch (e) {
            console.error('[RackChart] screenshot failed', e);
            return null;
        }
    },

    /** Subscribe to crosshair move */
    onCrosshair(element, callback) {
        const chart = REGISTRY.get(element);
        if (!chart) return;
        try {
            chart.subscribeAction('onCrosshairChange', callback);
        } catch (e) {
            console.warn('[RackChart] onCrosshair failed', e.message);
        }
    },

    /** Scroll to real-time (latest candle) */
    scrollToRealTime(element) {
        const chart = REGISTRY.get(element);
        if (!chart) return;
        try { chart.scrollToRealTime(); } catch (e) {}
    },

    /** Reset zoom (fit all data) */
    fitContent(element) {
        const chart = REGISTRY.get(element);
        if (!chart) return;
        try {
            // KLineChart: setBarSpace + scrollToRealTime approximates fitContent
            chart.resize();
        } catch (e) {}
    },

    /** Expose default params for external use */
    defaultParams: DEFAULT_PARAMS,
    theme: THEME,
};

// ─── Export to window ───
if (typeof window !== 'undefined') {
    window.RackChart = RackChart;
    window.klinecharts = klinecharts;
}

export default RackChart;
export { RackChart };
