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
                <delivery-month
                    v-for="(month, index) in dateRange"
                    :key="`month-${index}`"
                    :delivery-month="month"
                    :dates="dates"
                    :style="getRangeTranslation"
                />
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
                    <mobile-delivery-option
                        v-for="(date, index) in flatDateRange"
                        :key="`date-${index}`"
                        :delivery-option="date"
                        :dates="dates"
                    />
                </select>
            </div>
        </div>
    </div>
</template>

<script>
    import DeliveryMonth from './Delivery/DeliveryMonth.vue'
    import DeliveryFormatMixin from '../Mixins/DeliveryFormat'
    import MobileDeliveryOption from './Delivery/MobileDeliveryOption.vue'

    export default {
        name: 'DeliverySelector',

        components: {
            DeliveryMonth,
            MobileDeliveryOption,
        },

        mixins: [
            DeliveryFormatMixin,
        ],

        props: {
            /**
             * List of available delivery dates
             */
            dates: {
                type: Array,
                required: true,
            },
        },

        data() {
            return {
                rangeIndex: 0,
                mobileSelect: 'default',
            }
        },

        computed: {
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
             * Get selected delivery option
             *
             * @return {String | Boolean}
             */
            selectedDelivery() {
                if (this.cartCollection.shippingMethod.name) {
                    return this.cartCollection.shippingMethod.name
                }

                return false
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

            /**
             * Get distance to translate date ranges
             *
             * @return {Object}
             */
            getRangeTranslation() {
                const transalation = -100
                return { transform: `translateX(${this.rangeIndex * transalation}%)` }
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
