<template>
    <div class="checkout__discount">
        <label for="checkoutDiscount">Discount</label>
        <input id="checkoutDiscount" type="text" v-model="currentCode" aria-label="Add discount code" :aria-invalid="formFieldValid('code')">
        <button
            class="btn-primary btn--pre-loading"
            :class="formIsLoading ? 'btn--loading' : ''"
            :disabed="formIsLoading"
            type="submit"
            @click.prevent="applyDiscount"
        >Apply</button>
        <v-form-error field="code"></v-form-error>
        <span class="applied-message" v-if="message">{{ message }}</span>
    </div>
</template>

<script>
    import { FormMixin } from 'maxfactor-vue-support'

    export default {
        name: 'mx-cart-discount',

        mixins: [
            FormMixin,
        ],

        data() {
            return {
                code: '',
                message: '',
            }
        },

        computed: {
            currentCode: {
                get() {
                    return this.code || this.currentCheckout.discount.code
                },
                set(value) {
                    this.code = value
                },
            },

        },

        methods: {
            applyDiscount() {
                this.getForm(`/cart/discount/${this.currentCode}`)
                    .then((response) => {
                        if (response.data.code) {
                            this.message = 'Discount applied'
                            this.currentCheckout.discount = response.data
                        }
                    }).catch(() => console.log('The discount code could not be verified'))
            },
        },
    }
</script>
