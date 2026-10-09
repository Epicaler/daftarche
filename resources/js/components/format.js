const PERSIAN_DIGITS = /[۰-۹٠-٩]/g;

export function toLatinDigits(value) {
    return String(value ?? '').replace(PERSIAN_DIGITS, (d) => {
        const code = d.charCodeAt(0);
        return String(code >= 0x06f0 ? code - 0x06f0 : code - 0x0660);
    });
}

export function formatNumber(value) {
    const n = Number(value);
    return Number.isFinite(n) ? new Intl.NumberFormat('en-US').format(Math.round(n)) : '';
}

export function formatShort(value) {
    const n = Math.abs(Number(value) || 0);
    const sign = Number(value) < 0 ? '−' : '';
    const units = [
        [1e9, 'میلیارد'],
        [1e6, 'میلیون'],
        [1e3, 'هزار'],
    ];
    for (const [size, label] of units) {
        if (n >= size) {
            return `${sign}${parseFloat((n / size).toFixed(2))} ${label}`;
        }
    }
    return `${sign}${n}`;
}

export const currency = () => document.documentElement.dataset.currency || 'تومان';
