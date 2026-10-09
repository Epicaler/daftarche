import { toGregorian, toJalaali, jalaaliMonthLength, isValidJalaaliDate } from 'jalaali-js';
import { toLatinDigits } from './format';

const MONTHS = ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'];

const pad = (n) => String(n).padStart(2, '0');

function today() {
    const now = new Date();
    return toJalaali(now.getFullYear(), now.getMonth() + 1, now.getDate());
}

function parse(value) {
    const match = toLatinDigits(value).trim().match(/^(\d{4})[/-](\d{1,2})[/-](\d{1,2})$/);
    if (!match) return null;
    const [jy, jm, jd] = match.slice(1).map(Number);
    return isValidJalaaliDate(jy, jm, jd) ? { jy, jm, jd } : null;
}

/** Jalali calendar dropdown bound to a "YYYY/MM/DD" string via x-modelable="value". */
export default () => ({
    value: '',
    open: false,
    viewY: 1400,
    viewM: 1,
    weekdays: ['ش', 'ی', 'د', 'س', 'چ', 'پ', 'ج'],

    init() {
        this.syncView();
        this.$watch('value', () => this.syncView());
    },

    syncView() {
        const date = parse(this.value) || today();
        this.viewY = date.jy;
        this.viewM = date.jm;
    },

    get title() {
        return `${MONTHS[this.viewM - 1]} ${this.viewY}`;
    },

    get cells() {
        const { gy, gm, gd } = toGregorian(this.viewY, this.viewM, 1);
        const offset = (new Date(gy, gm - 1, gd).getDay() + 1) % 7; // Saturday = 0
        const selected = parse(this.value);
        const now = today();
        const cells = Array.from({ length: offset }, (_, i) => ({ key: `b${i}`, day: null }));

        for (let day = 1; day <= jalaaliMonthLength(this.viewY, this.viewM); day++) {
            const same = (d) => d && d.jy === this.viewY && d.jm === this.viewM && d.jd === day;
            cells.push({ key: `d${day}`, day, selected: same(selected), today: same(now), friday: (offset + day) % 7 === 0 });
        }

        return cells;
    },

    shift(by) {
        const index = this.viewY * 12 + (this.viewM - 1) + by;
        this.viewY = Math.floor(index / 12);
        this.viewM = (index % 12) + 1;
    },

    pick(day) {
        this.value = `${this.viewY}/${pad(this.viewM)}/${pad(day)}`;
        this.open = false;
    },

    pickToday() {
        const t = today();
        this.value = `${t.jy}/${pad(t.jm)}/${pad(t.jd)}`;
        this.open = false;
    },

    normalize(event) {
        const date = parse(event.target.value);
        this.value = date ? `${date.jy}/${pad(date.jm)}/${pad(date.jd)}` : toLatinDigits(event.target.value);
    },
});
