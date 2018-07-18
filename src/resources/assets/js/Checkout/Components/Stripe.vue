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
                token: null,
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
             */
            createToken(cardData) {
                if (this.token) {
                    /**
                     * if a token has already been generated, return this instead of requesting a
                     * new token. If we create a new token each time it can lead to duplicate
                     * payments if a user someone manages to click the button multiple times.
                     */
                    this.$emit('input', {
                        token: this.token,
                    })

                    return
                }

                this.stripe.createToken(this.card, cardData).then((result) => {
                    this.$set(this, 'token', result.token)
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

            /**
             * Reset the token if a change is made to the card details to ensure a new valid
             * token is requested from Stripe.
             */
            this.card.addEventListener('change', () => this.$set(this, 'token', null))

            this.eventHandler.$on('createToken', this.createToken)
        },

    }
</script>
