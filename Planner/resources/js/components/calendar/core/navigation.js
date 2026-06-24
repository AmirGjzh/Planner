import DateValue from "./DateValue";

/**
 * NavigationIndex
 *
 * Provides O(1) navigation (next/prev) over an ordered list of timestamps.
 * Uses an array for order and a Map for index lookup.
 */
class NavigationIndex {
    /**
     * @param {number[]} values - Ordered list of UTC timestamps (ms)
     */
    constructor(values) {
        /**
         * Ordered timestamps
         * @type {number[]}
         */
        this.list = values;

        /**
         * Map of timestamp -> index in list
         * @type {Map<number, number>}
         */
        this.map = new Map();

        for (let i = 0; i < values.length; i++) {
            this.map.set(values[i], i);
        }
    }

    /**
     * Get next timestamp in sequence
     * @param {number} value - Current timestamp
     * @returns {number|undefined} Next timestamp or undefined if at end/not found
     */
    next(value) {
        const i = this.map.get(value);
        if (i == null) return;
        return this.list[i + 1];
    }

    /**
     * Get previous timestamp in sequence
     * @param {number} value - Current timestamp
     * @returns {number|undefined} Previous timestamp or undefined if at start/not found
     */
    prev(value) {
        const i = this.map.get(value);
        if (i == null) return;
        return this.list[i - 1];
    }

    /**
     * Get next day as DateValue
     * @param {number} ts - Current timestamp
     * @returns {DateValue|null} Next DateValue or null if not available
     */
    nextDay(ts) {
        const next = this.next(ts);
        return next != null ? DateValue.fromTimestamp(next) : null;
    }

    /**
     * Get previous day as DateValue
     * @param {number} ts - Current timestamp
     * @returns {DateValue|null} Previous DateValue or null if not available
     */
    prevDay(ts) {
        const prev = this.prev(ts);
        return prev != null ? DateValue.fromTimestamp(prev) : null;
    }

    /**
     * Get next week (linear +7 index, not grid-aware)
     *
     * WARNING:
     * - This assumes a continuous list.
     * - If disabled days are removed, this is NOT true calendar "next week".
     *
     * @param {number} ts - Current timestamp
     * @returns {DateValue|null} DateValue 7 steps ahead or null if out of bounds
     */
    nextWeek(ts) {
        const i = this.map.get(ts);
        if (i == null) return null;

        const next = this.list[i + 7];

        return next != null ? DateValue.fromTimestamp(next) : null;
    }

    /**
     * Get previous week (linear -7 index, not grid-aware)
     *
     * WARNING:
     * - Not accurate if list is filtered (disabled days removed).
     *
     * @param {number} ts - Current timestamp
     * @returns {DateValue|null} DateValue 7 steps back or null if out of bounds
     */
    prevWeek(ts) {
        const i = this.map.get(ts);
        if (i == null) return null;

        const prev = this.list[i - 7];
        return prev != null ? DateValue.fromTimestamp(prev) : null;
    }
}

export default NavigationIndex;