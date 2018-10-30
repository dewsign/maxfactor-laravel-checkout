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
    export default {
        name: 'DeliveryWeek',

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
