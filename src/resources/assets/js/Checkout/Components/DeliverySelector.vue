<template>
    <div class="delivery">
        <ul class="headings">
            <li
                v-for="day in dayNames"
                :key=day
            >
                {{ day }}
            </li>
        </ul>
        <ul>
            <li
                v-for="date in dateRange"
                :key=date.name
            >
                <button
                    class="button"
                    :disabled="!getDelivery(date)"
                    v-on:click.prevent="updatePostage(getDelivery(date))"
                >
                    {{ formatDate(date) }}
                    {{ formatPrice(getDelivery(date)['price']) }}
                </button>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        name: 'DeliverySelector',

        props: {
            /**
             * List of delivery dates
             */
            dates: {
                type: Array,
                required: true,
            },
        },
        
        data() {
            return {
                days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            }
        },

        computed: {
            /**
             * Return list of day names for column headings
             *
             * @return {Array}
             */
            dayNames() {
                return this.dateRange.slice(0, 7).map(o => this.days[o.getDay()])
            },

            /**
             * Return list of all dates between start and end delivery date
             *
             * @return {Array}
             */
            dateRange() {
                const startDate = new Date(this.dates[0].date)
                const endDate = new Date(this.dates[this.dates.length - 1].date)

                let currentDate = startDate

                let rangeOfDates = new Array()

                while (currentDate <= endDate) {
                    rangeOfDates.push(currentDate)
                    currentDate = this.addDay(currentDate)
                }
                
                return rangeOfDates
            },

            /**
             * Return list of localised dates
             *
             * @return {Array}
             */
            localeDates() {
                return this.dates.map(o => o['localeDate'])
            }
        },

        methods: {
            /**
             * Get day of week ordinal suffix
             *
             * @return {String}
             */
            getOrdinal(n) {
                return n + (n > 0 ? ['th', 'st', 'nd', 'rd'][(n > 3 && n < 21) || n % 10 > 3 ? 0 : n % 10] : '');
            },

            /**
             * Add a day to date
             * TODO: refactor to a helper package
             *
             * @return {Date}
             */
            addDay(date) {
                var result = new Date(date)
                result.setDate(result.getDate() + 1)
                return result
            },

            /**
             * Get delivery option object from date string
             *
             * @return {Date}
             */
            getDelivery(date) {
                const inputDate = new Date(date).toLocaleDateString()

                if (this.localeDates.includes(inputDate)) {
                    return this.dates.find(date => date.localeDate === inputDate)
                }

                return false
            },

            /**
             * Format a price to display on front end
             *
             * @return {String}
             */
            formatPrice(price) {
                if (typeof price === 'undefined') {
                    return 'Unavailable'
                }

                if (price === 0) {
                    return 'Free'
                }

                return price.toLocaleString('en-GB', {
                        style: 'currency',
                        currency: 'GBP',
                        currencyDisplay: 'symbol',
                    })
            },

            /**
             * Format a date to display on front end
             *
             * @return {String}
             */
            formatDate(date) {
                const shortMonth = date.toLocaleDateString("en-US", { month: 'short' })

                return this.getOrdinal(date.getDate()) + ' ' + shortMonth
            },

            /**
             * Update postage when an option is clicked
             *
             * @return {String}
             */
            updatePostage(shippingMethod) {
                this.$set(this.cartCollection, 'shippingMethod', shippingMethod)
            },
        },
    }
</script>
