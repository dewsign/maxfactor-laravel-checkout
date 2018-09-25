<template>
    <div class="checkout__delivery-selector">
        <div class="checkout__headings">
            <div
                v-for="day in dayNames"
                :key=day
                class="heading"
            >
                {{ day }}
            </div>
        </div>

        <div class="checkout__delivery-grid">
            <div
                v-for="week in dateRange"
                class="checkout__week"
            >
                <div
                    v-for="date in week"
                    :key=date.name
                    class="checkout__option"
                >
                    <button
                        v-bind:class="{ selected: isSelected(date) }"
                        :disabled="!getDelivery(date)"
                        v-on:click.prevent="updatePostage(getDelivery(date))"
                    >
                        <span>{{ formatDate(date)['day'] }}</span>
                        <span>{{ formatDate(date)['month'] }}</span>
                        <span class="checkout__delivery-price">{{ formatPrice(getDelivery(date)['price']) }}</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="checkout__delivery-confirmation" v-if="selectedDelivery">
            You've selected delivery on <span>{{ selectedDelivery }}</span>
        </div>
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
                days: ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'],
            }
        },

        computed: {
            /**
             * Return list of day names for column headings
             *
             * @return {Array}
             */
            dayNames() {
                return this.dateRange[0].map(o => this.days[o.getDay()])
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
                let weekRange = new Array()

                while (currentDate <= endDate) {
                    for (var i = 0; i < 7; i++) {
                        weekRange.push(currentDate)
                        currentDate = this.addDay(currentDate)
                    }
                    rangeOfDates.push(weekRange)

                    weekRange = new Array()
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
            },

            /**
             * Return list of localised dates
             *
             * @return {Array}
             */
            selectedDelivery() {
                if (this.cartCollection.shippingMethod.name) {
                    return this.cartCollection.shippingMethod.name
                }

                return false
            },
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
             * @return {Array}
             */
            formatDate(date) {
                const shortMonth = date.toLocaleDateString("en-UK", { month: 'short' })

                return { 'day': this.getOrdinal(date.getDate()), 'month': shortMonth }
            },

            /**
             * Update postage when an option is clicked
             *
             * @return {String}
             */
            updatePostage(shippingMethod) {
                this.$set(this.cartCollection, 'shippingMethod', shippingMethod)
            },

            /**
             * Check if a postage option is selected
             *
             * @return {String}
             */
            isSelected(postageOption) {
                const inputDate = new Date(postageOption).toLocaleDateString()

                return inputDate === this.cartCollection.shippingMethod.localeDate
            }
        },
    }
</script>
