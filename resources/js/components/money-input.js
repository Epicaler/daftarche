import { formatNumber, formatShort, toLatinDigits, currency } from './format';

const digits = (value) => toLatinDigits(value).replace(/\D/g, '').replace(/^0+(?=\d)/, '');

/** Amount field that shows thousand separators while keeping a plain digit string in `value`. */
export default () => ({
    value: '',
    display: '',

    init() {
        this.display = this.format(this.value);
        this.$watch('value', (v) => {
            if (digits(this.display) !== String(v ?? '')) this.display = this.format(v);
        });
    },

    format(v) {
        const raw = digits(v);
        return raw === '' ? '' : formatNumber(raw);
    },

    onInput(event) {
        const raw = digits(event.target.value);
        this.value = raw;
        this.display = this.format(raw);
        event.target.value = this.display;
    },

    get hint() {
        return this.value ? `${formatShort(this.value)} ${currency()}` : '';
    },
});
