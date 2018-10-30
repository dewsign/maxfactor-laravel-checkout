export default {
    computed: {
        /**
         * Get delivery option object from date string
         *
         * @return {Date}
         */
        deliveryItem() {
            const inputDate = this.deliveryOption.toLocaleDateString('en-GB').replace(/\u200E/g, '')

            if (this.localeDates.includes(inputDate)) {
                return this.dates.find(dates => dates.localeDate === inputDate)
            }

            return false
        },

        /**
         * Return list of localised dates
         *
         * @return {Array}
         */
        localeDates() {
            return this.dates.map(o => o.localeDate)
        },
    },
}
