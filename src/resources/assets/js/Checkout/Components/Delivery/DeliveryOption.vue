<template>
    <div class="delivery-selector__option">
        <button
            :disabled="!delivery"
            :class="{ selected: isSelected }"
            class="button-option"
            @click.prevent="updatePostage"
        >
            <span>{{ deliveryDay }}</span>
            <span>{{ deliveryMonth }}</span>
            <span class="price">{{ formattedPrice }}</span>
        </button>
    </div>
</template>

<script>
    export default {
        name: 'DeliveryOption',

        props: {
            deliveryOption: {
                type: Date,
                required: true,
            },

            /**
             * List of available delivery dates
             */
            dates: {
                type: Array,
                required: true,
            },
        },

        computed: {
            /**
             * Get delivery option object from date string
             *
             * @return {Date}
             */
            delivery() {
                const inputDate = new Date(this.deliveryOption).toLocaleDateString('en-GB').replace(/\u200E/g, '')

                if (this.localeDates.includes(inputDate)) {
                    return this.dates.find(dates => dates.localeDate === inputDate)
                }

                return false
            },

            /**
             * Check if a postage option is selected
             *
             * @return {String}
             */
            isSelected() {
                const inputDate = new Date(this.deliveryOption).toLocaleDateString('en-GB').replace(/\u200E/g, '')

                return inputDate === this.cartCollection.shippingMethod.localeDate
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
                return this.getOrdinal(this.deliveryOption.getDate())
            },

            /**
             * Formatted price to display on front end
             *
             * @return {String}
             */
            formattedPrice() {
                if (typeof this.delivery.price === 'undefined') {
                    return 'Unavailable'
                }

                if (this.delivery.price === 0) {
                    return 'Free'
                }

                return this.delivery.price.toLocaleString('en-GB', {
                    style: 'currency',
                    currency: 'GBP',
                    currencyDisplay: 'symbol',
                }).replace(/\u200E/g, '')
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
             * Update postage when an option is clicked
             *
             * @return {String}
             */
            updatePostage() {
                this.$set(this.cartCollection, 'shippingMethod', this.delivery)
            },
        },
    }
</script>
