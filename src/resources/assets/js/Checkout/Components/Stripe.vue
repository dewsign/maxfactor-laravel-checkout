<template>
    <div>
        <slot></slot>
        <div id="card-element"></div>
    </div>
</template>

<script>
    export default {
        props: [
            'publishablekey',
        ],

        data() {
            return {
                stripe: null,
                elements: null,
                options: {
                    hidePostalCode: true,
                    style: {
                        base: {
                            fontSize: '19px',
                            iconColor: '#2d2926',
                            color: '#2d2926',
                        },
                    },
                },
                element: [],
                card: null,
            }
        },

        computed: {
            //
        },

        methods: {
            /**
             * Send the token request to Stripe.
             * TODO: emit input/update event to update v-model with callback
             */
            createToken(cardData) {
                this.stripe.createToken(this.card, cardData).then((result) => {
                    this.$emit('input', result)
                })
            },
        },

        /**
         * Initialise Stripe.
         * TODO: Split out into methods and tidy up
         */
        mounted() {
            this.stripe = window.Stripe(this.publishablekey)

            this.elements = this.stripe.elements()

            this.card = this.elements.create('card', this.options)
            this.card.mount('#card-element')

            this.eventHandler.$on('createToken', this.createToken)
        },

    }
</script>
