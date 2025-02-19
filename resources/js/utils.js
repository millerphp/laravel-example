/**
 * Format a number as GBP currency
 */
export function formatCurrency(amount) {
    if (amount === null || amount === undefined || isNaN(amount)) {
        console.warn('Invalid amount for currency formatting:', amount);
        return '£0.00';
    }
    
    const number = typeof amount === 'string' ? parseFloat(amount) : amount;
    
    if (isNaN(number)) {
        console.warn('Failed to parse amount for currency formatting:', amount);
        return '£0.00';
    }

    return new Intl.NumberFormat('en-GB', {
        style: 'currency',
        currency: 'GBP',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(number);
}

/**
 * Format a percentage
 */
export function formatPercentage(value) {
    return `${value}%`;
}

/**
 * Calculate percentage difference between two numbers
 */
export function calculatePercentageDiff(original, final) {
    if (original === 0) return 0;
    return ((original - final) / original) * 100;
} 