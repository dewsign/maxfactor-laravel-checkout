<template>
    <div class="delivery-selector__delivery-grid">
        <delivery-week
            v-for="(week, index) in deliveryMonth"
            :key="`week-${index}`"
            :delivery-week="week"
            :dates="dates"
        />
    </div>
</template>

<script>
    import DeliveryWeek from './DeliveryWeek.vue'

    export default {
        name: 'DeliveryMonth',

        components: {
            DeliveryWeek,
        },

        props: {
            deliveryMonth: {
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
