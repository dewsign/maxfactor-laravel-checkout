<template>
    <div class="delivery-selector__week">
        <delivery-option
            v-for="(date, index) in deliveryWeek"
            :key="`date-${index}`"
            :delivery-option="date"
            :dates="dates"
        />
    </div>
</template>

<script>
    import DeliveryOption from './DeliveryOption.vue'

    export default {
        name: 'DeliveryWeek',

        components: {
            DeliveryOption,
        },

        props: {
            deliveryWeek: {
                type: Array,
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
    }
</script>
