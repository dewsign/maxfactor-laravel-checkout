<template>
    <div class="delivery-selector">
        <div class="delivery-selector__desktop">
            <div class="delivery-selector__headings">
                <div
                    v-for="day in dayNames"
                    :key="day"
                    class="heading"
                >
                    {{ day }}
                </div>
            </div>

            <div class="delivery-selector__selection-window">
                <div
                    v-for="(month, index) in dateRange"
                    :key="`month-${index}`"
                    :style="getRangeTranslation"
                    class="delivery-selector__delivery-grid"
                >
                    <div
                        v-for="(week, index) in month"
                        :key="`week-${index}`"
                        class="delivery-selector__week"
                    >
                        <div
                            v-for="(date, index) in week"
                            :key="`date-${index}`"
                            class="delivery-selector__option"
                        >
                            <button
                                :disabled="!getDelivery(date)"
                                :class="{ selected: isSelected(date) }"
                                class="button-option"
                                @click.prevent="updatePostage(getDelivery(date))"
                            >
                                <span>{{ formatDate(date)['day'] }}</span>
                                <span>{{ formatDate(date)['month'] }}</span>
                                <span class="price">
                                    {{ formatPrice(getDelivery(date)['price']) }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-if="!disablePrevControl || !disableNextControl"
                class="delivery-selector__controls"
            >
                <button
                    :disabled="disablePrevControl"
                    class="previous"
                    @click.prevent="decRangeIndex"
                >Previous</button>
                <button
                    :disabled="disableNextControl"
                    class="next"
                    @click.prevent="incRangeIndex"
                >Show more</button>
            </div>

            <div
                v-if="selectedDelivery"
                class="delivery-selector__confirmation"
            >
                You've selected delivery on <span>{{ selectedDelivery }}</span>
            </div>
        </div>

        <div class="delivery-selector__mobile">
            <h4>When would you like your delivery?</h4>
            <div class="delivery-selector__select-wrapper">
                <select v-model="mobileSelect">
                    <option
                        disabled
                        value="default"
                    >Select your delivery date</option>
                    <option
                        v-for="(date, index) in flatDateRange"
                        :key="`date-${index}`"
                        :disabled="!getDelivery(date)"
                        :value="getDelivery(date)"
                    >
                        {{ getMobileOption(date) }}
                    </option>
                </select>
            </div>
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
                days: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                rangeIndex: 0,
                mobileSelect: 'default',
            }
        },

        computed: {
            /**
             * Return list of day names for column headings
             *
             * @return {Array}
             */
            dayNames() {
                return this.dateRange[0][0].map(o => this.days[o.getDay()])
            },

            /**
             * Return list of all dates between start and end delivery date
             * Grouped for display
             *
             * @return {Array}
             */
            dateRange() {
                const startDate = new Date(this.dates[0].date)
                const endDate = new Date(this.dates[this.dates.length - 1].date)

                let currentDate = startDate
                const rangeOfDates = []
                let weekRange = []
                let monthRange = []

                while (currentDate <= endDate) {
                    for (let i = 0; i < 28; i += 1) {
                        weekRange.push(currentDate)
                        currentDate = this.addDay(currentDate)

                        if (i % 7 === 6) {
                            monthRange.push(weekRange)
                            weekRange = []
                        }
                    }

                    rangeOfDates.push(monthRange)
                    monthRange = []
                }

                return rangeOfDates
            },

            /**
             * Return flat list of all dates between start and end delivery date
             *
             * @return {Array}
             */
            flatDateRange() {
                const startDate = new Date(this.dates[0].date)
                const endDate = new Date(this.dates[this.dates.length - 1].date)

                let currentDate = startDate
                const flatRange = []

                while (currentDate <= endDate) {
                    flatRange.push(currentDate)
                    currentDate = this.addDay(currentDate)
                }

                return flatRange
            },

            /**
             * Return list of localised dates
             *
             * @return {Array}
             */
            localeDates() {
                return this.dates.map(o => o.localeDate)
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

            /**
             * Get distance to translate date ranges
             *
             * @return {Object}
             */
            getRangeTranslation() {
                const transalation = -100
                return { transform: `translateX(${this.rangeIndex * transalation}%)` }
            },

            /**
             * Maximum index for date ranges
             * Think of this as max index of 'months'
             *
             * @return {Integer}
             */
            maxRangeIndex() {
                return this.dateRange.length - 1
            },

            /**
             * Should the next dates control be disabled
             *
             * @return {Boolean}
             */
            disableNextControl() {
                return this.rangeIndex >= this.maxRangeIndex
            },

            /**
             * Should the prev dates control be disabled
             *
             * @return {Boolean}
             */
            disablePrevControl() {
                return this.rangeIndex <= 0
            },
        },

        watch: {
            mobileSelect: {
                handler() {
                    // If shipping method is set in cart and not data, update data
                    if (this.cartCollection.shippingMethod.date && !this.mobileSelect.date) {
                        this.mobileSelect = this.cartCollection.shippingMethod
                    }

                    if (this.mobileSelect.date) {
                        this.$set(this.cartCollection, 'shippingMethod', this.mobileSelect)
                    }
                },
                immediate: true,
            },
        },

        methods: {
            /**
             * Get day of week ordinal suffix
             *
             * @return {String}
             */
            getOrdinal(n) {
                return n + (n > 0 ? ['th', 'st', 'nd', 'rd'][(n > 3 && n < 21) || n % 10 > 3 ? 0 : n % 10] : '')
            },

            /**
             * Add a day to date
             *
             * @return {Date}
             */
            addDay(date) {
                const result = new Date(date)
                result.setDate(result.getDate() + 1)
                return result
            },

            /**
             * Get delivery option object from date string
             *
             * @return {Date}
             */
            getDelivery(date) {
                const inputDate = new Date(date).toLocaleDateString('en-GB').replace(/\u200E/g, '')

                if (this.localeDates.includes(inputDate)) {
                    return this.dates.find(dates => dates.localeDate === inputDate)
                }

                return false
            },

            /**
             * Get delivery option in format required for mobile
             *
             * @return {Date}
             */
            getMobileOption(date) {
                const deliveryOption = this.getDelivery(date)

                return `${this.formatDate(date).mobile} - ${this.formatPrice(deliveryOption.price)}`
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
                const shortMonth = date.toLocaleDateString('en-UK', { month: 'short' }).replace(/\u200E/g, '')
                const mobileString = `${this.days[date.getDay()]} ${this.getOrdinal(date.getDate())} ${shortMonth}`

                return {
                    day: this.getOrdinal(date.getDate()),
                    month: shortMonth,
                    mobile: mobileString,
                }
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
                const inputDate = new Date(postageOption).toLocaleDateString('en-GB').replace(/\u200E/g, '')

                return inputDate === this.cartCollection.shippingMethod.localeDate
            },

            /**
             * Increase the range index
             *
             * @return {Void}
             */
            incRangeIndex() {
                if (this.rangeIndex < this.maxRangeIndex) {
                    this.rangeIndex += 1
                }
            },

            /**
             * Decrease the range index
             *
             * @return {Void}
             */
            decRangeIndex() {
                if (this.rangeIndex > 0) {
                    this.rangeIndex -= 1
                }
            },
        },
    }
</script>
