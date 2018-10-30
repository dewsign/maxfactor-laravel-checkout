<template>
    <option
        :disabled="!deliveryItem"
        :value="deliveryItem"
    >
        {{ mobileOption }}
    </option>
</template>

<script>
    import DeliveryItemMixin from '../../Mixins/DeliveryItem'
    import DeliveryFormatMixin from '../../Mixins/DeliveryFormat'

    export default {
        name: 'DeliveryWeek',

        mixins: [
            DeliveryItemMixin,
            DeliveryFormatMixin,
        ],

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
             * Get delivery option in format required for mobile
             *
             * @return {Date}
             */
            mobileOption() {
                return `${this.deliveryDateMobile} - ${this.formattedPrice}`
            },
        },
    }
</script>
