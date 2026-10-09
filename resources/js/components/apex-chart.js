import { formatNumber, formatShort, currency } from './format';

const INK = '#141c17';
const MUTED = '#8b938e';
const GRID = '#eceeea';
const money = (v) => `${formatNumber(v)} ${currency()}`;
// ApexCharts draws SVG text left-to-right, which turns "80 میلیون" into "میلیون 80".
// Wrapping in Right-to-Left Isolate … Pop Directional Isolate lays the string out RTL without visible characters.
const rtl = (s) => `\u2067${s}\u2069`;
const shortRtl = (v) => rtl(formatShort(v));
const escape = (s) => String(s).replace(/[&<>"']/g, (c) => `&#${c.charCodeAt(0)};`);

/** Tooltip markup rendered by us (not ApexCharts) so Persian text, colons and units flow right-to-left. */
const tipRow = (color, name, value, extra = '') =>
    `<div class="chart-tip-row"><span class="chart-tip-dot" style="background:${color}"></span>` +
    `<span class="chart-tip-name">${escape(name)}</span><span class="chart-tip-value">${money(value)}${extra}</span></div>`;

const base = () => ({
    chart: {
        fontFamily: "'Vazirmatn FD', sans-serif",
        foreColor: MUTED,
        toolbar: { show: false },
        zoom: { enabled: false },
        animations: { speed: 400 },
        parentHeightOffset: 0,
    },
    dataLabels: { enabled: false },
    // ApexCharts' SVG legend doesn't lay out RTL text correctly; the legend is HTML (see x-chart).
    legend: { show: false },
    grid: { borderColor: GRID, strokeDashArray: 4, padding: { left: 8, right: 8 } },
});

const builders = {
    /** Income vs expense per month: grouped thin bars sharing one axis. */
    trend: ({ labels, income, expense, height = 300 }) => ({
        ...base(),
        chart: { ...base().chart, type: 'bar', height },
        series: [
            { name: 'درآمد', data: income },
            { name: 'هزینه', data: expense },
        ],
        colors: ['#217249', '#c9761f'],
        plotOptions: { bar: { columnWidth: '52%', borderRadius: 4, borderRadiusApplication: 'end' } },
        stroke: { show: true, width: 2, colors: ['transparent'] },
        xaxis: { categories: labels, axisBorder: { show: false }, axisTicks: { show: false }, labels: { rotate: 0, hideOverlappingLabels: true, trim: false } },
        yaxis: { labels: { formatter: shortRtl, offsetX: -12 } },
        states: { hover: { filter: { type: 'darken', value: 0.9 } } },
        tooltip: {
            shared: true,
            intersect: false,
            custom: ({ dataPointIndex, w }) => {
                const rows = w.config.series
                    .map((s, i) => (w.globals.collapsedSeriesIndices.includes(i) ? '' : tipRow(w.globals.colors[i], s.name, s.data[dataPointIndex])))
                    .join('');
                return `<div class="chart-tip" dir="rtl"><div class="chart-tip-title">${escape(w.globals.labels[dataPointIndex])}</div>${rows}</div>`;
            },
        },
    }),

    /** Share per category. */
    donut: ({ labels, values, colors, height = 280, title = 'جمع' }) => ({
        ...base(),
        chart: { ...base().chart, type: 'donut', height },
        series: values,
        labels,
        colors,
        stroke: { width: 2, colors: ['#fff'] },
        plotOptions: {
            pie: {
                donut: {
                    size: '72%',
                    labels: {
                        show: true,
                        name: { fontSize: '12px', color: MUTED, offsetY: -4 },
                        value: { fontSize: '17px', fontWeight: 700, color: INK, offsetY: 6, formatter: shortRtl },
                        total: { show: true, label: title, color: MUTED, formatter: (w) => shortRtl(w.globals.seriesTotals.reduce((a, b) => a + b, 0)) },
                    },
                },
            },
        },
        tooltip: {
            custom: ({ series, seriesIndex, w }) => {
                const total = series.reduce((a, b) => a + b, 0) || 1;
                const percent = ` <span class="chart-tip-muted">(${Math.round((series[seriesIndex] / total) * 100)}٪)</span>`;
                return `<div class="chart-tip" dir="rtl">${tipRow(w.globals.colors[seriesIndex], w.globals.labels[seriesIndex], series[seriesIndex], percent)}</div>`;
            },
        },
    }),
};

export default (config) => ({
    chart: null,
    legend: [],

    async init() {
        const options = builders[config.type](config);
        const total = config.type === 'donut' ? config.values.reduce((a, b) => a + Number(b), 0) || 1 : 0;

        this.legend =
            config.type === 'donut'
                ? config.labels.map((name, i) => ({
                      name,
                      color: config.colors[i],
                      value: formatShort(config.values[i]),
                      percent: `${Math.round((config.values[i] / total) * 100)}٪`,
                      hidden: false,
                  }))
                : options.series.map((s, i) => ({ name: s.name, color: options.colors[i], hidden: false }));

        // Loaded on demand so pages without charts stay light.
        const { default: ApexCharts } = await import('apexcharts');
        if (!this.$el.isConnected) return;
        this.chart = new ApexCharts(this.$refs.canvas, options);
        this.chart.render();
    },

    toggle(index) {
        if (!this.chart) return;
        const item = this.legend[index];

        // A legend must never hide every series at once
        if (!item.hidden && this.legend.filter((l) => !l.hidden).length === 1) return;
        item.hidden = !item.hidden;

        if (config.type === 'donut') {
            this.chart.updateSeries(config.values.map((v, i) => (this.legend[i].hidden ? 0 : Number(v))));
        } else {
            this.chart.toggleSeries(item.name);
        }
    },

    destroy() {
        this.chart?.destroy();
    },
});
