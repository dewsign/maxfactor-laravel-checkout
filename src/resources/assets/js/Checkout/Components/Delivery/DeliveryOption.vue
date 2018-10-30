<template>
    <div class="delivery-selector__option">
        <button
            :disabled="!deliveryItem"
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
    import DeliveryItemMixin from '../../Mixins/DeliveryItem'
    import DeliveryFormatMixin from '../../Mixins/DeliveryFormat'

    export default {
        name: 'DeliveryOption',

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
             * Check if a postage option is selected
             *
             * @return {String}
             */
            isSelected() {
                const inputDate = this.deliveryOption.toLocaleDateString('en-GB').replace(/\u200E/g, '')

                return inputDate === this.cartCollection.shippingMethod.localeDate
            },
        },

        methods: {
            /**
             * Update postage when an option is clicked
             *
             * @return {String}
             */
            updatePostage() {
                this.$set(this.cartCollection, 'shippingMethod', this.deliveryItem)
            },
        },
    }
</script>
