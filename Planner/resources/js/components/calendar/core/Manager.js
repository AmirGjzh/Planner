import Month from './Month';
import DateValue from './DateValue';

/**
 * Manages calendar rendering, navigation, and day selection
 */
class Manager {

    /**
     * Navigate to next month
     * @static
     * @param {DateValue} baseDate - Current base date
     * @returns {DateValue} New base date
     */
    static nextMonth(baseDate) {
        return baseDate.addMonth();
    }

    /**
     * Navigate to previous month
     * @static
     * @param {DateValue} baseDate - Current base date
     * @returns {DateValue} New base date
     */
    static prevMonth(baseDate) {
        return baseDate.subMonth();
    }

    /**
     * Navigate to today
     * @static
     * @returns {DateValue} Current date
     */
    static goToToday() {
        return DateValue.today();
    }

    /**
     * Render one or more months with days and blanks
     * @static
     * @param {Object} options - Render options
     * @param {DateValue} options.baseDate - Starting month to render
     * @param {number} options.numberOfMonths - How many months to render
     * @param {boolean} [options.fixedWeeks] - Whether to use fixed week heights
     * @param {string|null} [options.min] - Minimum date constraint as ISO string
     * @param {string|null} [options.max] - Maximum date constraint as ISO string
     * @param {string[]} [options.unavailableDates] - Array of unavailable dates
     * @param {string} [options.locale] - BCP 47 language tag
     * @returns {Array} Array of month objects with days and blanks
     */
    static render({ baseDate, numberOfMonths, fixedWeeks = false, min = undefined, max = undefined, unavailableDates = [], locale = undefined, startDay = undefined, specialDays = {}, specialDisabled = [], specialTooltips = {}, }) {
        const rendered = [];

        const maxWeeks = fixedWeeks ? this.calculateMinWeeksBetweenRenderedMonths(baseDate.getYear(), baseDate.getMonth(), numberOfMonths, startDay) : null;

        for (let i = 0; i < numberOfMonths; i++) {

            const date = baseDate.addMonths(i);

            const month = new Month(date, maxWeeks, min, max, unavailableDates, locale, startDay, specialDays, specialDisabled, specialTooltips);

            rendered.push(month.toPlainObject());
        }

        return rendered;
    }

    /**
     * Navigate to next day with month boundary detection
     * @static
     * @param {Object} day - Current day object
     * @param {DateValue} baseDate - Current base date
     * @param {number} numberOfMonths - Number of renderedmonths for boundary detection
     * @returns {Object} Object with next day key and updated baseDate
     */
    static nextDay(focused, baseDate, navigationIndex, numberOfMonths) {
        const currentDay = focused.dateValue;

        const nextFocusedDay = navigationIndex.nextDay(currentDay.getTimestamp());

        if (this.isThisNextMonthAfterOther(currentDay, nextFocusedDay, focused.index, numberOfMonths)) {
            baseDate = this.nextMonth(baseDate);
        }

        return { key: nextFocusedDay.getTimestamp(), baseDate };
    }
    /**
     * Navigate to previous day with month boundary detection
     * @static
     * @param {Object} day - Current day object
     * @param {DateValue} baseDate - Current base date
     * @returns {Object} Object with previous day key and updated baseDate
     */
    static previousDay(focused, baseDate, navigationIndex) {

        const currentDay = focused.dateValue;

        const previousDay = navigationIndex.prevDay(currentDay.getTimestamp());

        if (this.isThisPrevMonthHiddenFromUI(currentDay, previousDay, focused.index)) {

            baseDate = this.prevMonth(baseDate);
        }

        return { key: previousDay.getTimestamp(), baseDate };
    }

    /**
     * Navigate to same day in next week with month boundary detection
     * @static
     * @param {Object} day - Current day object
     * @param {DateValue} baseDate - Current base date
     * @param {number} numberOfMonths - Number of rendered months for boundary detection
     * @returns {Object} Object with next week day key and updated baseDate
     */
    static nextDayInNextWeek(focused, baseDate, navigationIndex, numberOfMonths) {

        const currentDay = focused.dateValue;

        const nextDayInNextWeek = navigationIndex.nextWeek(currentDay.getTimestamp());

        if (this.isThisNextMonthAfterOther(currentDay, nextDayInNextWeek, focused.index, numberOfMonths)) {
            baseDate = this.nextMonth(baseDate);
        }

        return { key: nextDayInNextWeek.getTimestamp(), baseDate };
    }

    /**
     * Navigate to same day in previous week with month boundary detection
     * @static
     * @param {Object} day - Current day object
     * @param {DateValue} baseDate - Current base date
     * @returns {Object} Object with previous week day key and updated baseDate
     */
    static previousDayInPreviousWeek(focused, baseDate, navigationIndex) {

        const currentDay = focused.dateValue;

        const previousDayInPreviousWeek = navigationIndex.prevWeek(currentDay.getTimestamp());

        if (this.isThisPrevMonthHiddenFromUI(currentDay, previousDayInPreviousWeek, focused.index)) {
            baseDate = this.prevMonth(baseDate);
        }

        return { key: previousDayInPreviousWeek.getTimestamp(), baseDate };
    }

    /**
     * Check if navigation crosses into next month (at end of visible months)
     * @static
     * @private
     * @param {DateValue} before - Original date
     * @param {DateValue} other - Navigated date
     * @param {number} index - Current month index
     * @param {number} numberOfMonths - Total number of rendered months
     * @returns {boolean}
     */
    static isThisNextMonthAfterOther(before, other, index, numberOfMonths) {

        if (before.getYear() < other.getYear() && numberOfMonths - 1 === index) {
            return true;
        }

        if (before.getMonth() < other.getMonth() && numberOfMonths - 1 === index) {
            return true
        }

        return false;
    }

    /**
     * Check if navigation crosses into previous month (at start of visible months)
     * @static
     * @private
     * @param {DateValue} before - Original date
     * @param {DateValue} other - Navigated date
     * @param {number} index - Current month index
     * @returns {boolean}
     */
    static isThisPrevMonthHiddenFromUI(before, other, index) {

        if (before.getYear() > other.getYear() && 0 === index) {
            return true;
        }

        if (before.getMonth() > other.getMonth() && 0 === index) {
            return true
        }

        return false;
    }

    /**
     * Calculate the maximum number of weeks needed across a range of months
     * Used for fixed-height calendar grids
     * @static
     * @param {number} year - Starting year
     * @param {number} month - Starting month (1-12)
     * @param {number} numberOfMonths - Number of months to check
     * @returns {number} Maximum weeks needed
     */
    static calculateMinWeeksBetweenRenderedMonths(year, month, numberOfMonths, startDay) {
        let maxWeeks = 0;

        for (let i = 0; i < numberOfMonths; i++) {
            const date = DateValue.fromParts(year, month).addMonths(i);

            maxWeeks = Math.max(maxWeeks, date.getWeeksInMonth(startDay));
        }

        return maxWeeks;
    }

    static canNavigateTo(baseDate, options) {

        const { numberOfMonths, minDate, maxDate, rangeOptions } = options;

        const firstDay = baseDate.startOfMonth();
        const lastDay = baseDate.addMonths(numberOfMonths - 1).lastOfMonth();

        // standard min/max date constraints
        if (minDate && lastDay.isBefore(minDate)) return false;
        if (maxDate && firstDay.isAfter(maxDate)) return false;

        // range maxRange navigation constraint
        // when user has selected range start, block navigation to months
        // that are entirely outside the reachable window from that start date
        if (rangeOptions) {
            const { firstSelected, maxRange } = rangeOptions;

            if (firstSelected && maxRange !== null) {
                // farthest forward the user can select from the start
                const maxForward = firstSelected.addDays(maxRange);
                // farthest backward the user can select from the start  
                const maxBackward = firstSelected.subDays(maxRange);

                // the candidate month is entirely beyond forward reach
                if (firstDay.isAfter(maxForward)) return false;

                // the candidate month is entirely before backward reach
                if (lastDay.isBefore(maxBackward)) return false;
            }
        }

        return true;
    }
}

export default Manager;