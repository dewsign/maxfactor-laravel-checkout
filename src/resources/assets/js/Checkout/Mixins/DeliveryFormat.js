import { DateOrdinal } from 'maxfactor-vue-support'

export default {
    data() {
        return {
            days: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        }
    },

    computed: {
        /**
         * Delivery month to display on the front end
         *
         * @return {String}
         */
        deliveryMonth() {
            return this.deliveryOption.toLocaleDateString('en-UK', { month: 'short' }).replace(/\u200E/g, '')
        },

        /**
         * Delivery day to display on the front end
         *
         * @return {String}
         */
        deliveryDay() {
            return DateOrdinal(this.deliveryOption.getDate())
        },

        /**
         * Mobile delivery to display on the front end
         *
         * @return {String}
         */
        deliveryDateMobile() {
            return `${this.days[this.deliveryOption.getDay()]} ${DateOrdinal(this.deliveryOption.getDate())} ${this.deliveryMonth}`
        },

        /**
         * Formatted price to display on front end
         *
         * @return {String}
         */
        formattedPrice() {
            if (typeof this.deliveryItem.price === 'undefined') {
                return 'Unavailable'
            }

            if (this.deliveryItem.price === 0) {
                return 'Free'
            }

            return this.deliveryItem.price.toLocaleString('en-GB', {
                style: 'currency',
                currency: 'GBP',
                currencyDisplay: 'symbol',
            }).replace(/\u200E/g, '')
        },

        /**
         * Return ordered list of day names for column headings
         *
         * @return {Array}
         */
        dayNames() {
            return this.dateRange[0][0].map(o => this.days[o.getDay()])
        },

    },
}
