<template>
    <ul class="cart__item">
        <li v-if="!isModalCart">
            <img :src="cartItem.image" :alt="cartItem.name">
        </li>

        <li v-if="isFullCart">
            <a :href="cartItem.url" class="product-details">Reference: {{ cartItem.id }}
                <h3>{{ cartItem.name }}</h3>
            </a>
        </li>

        <li v-if="!isFullCart">
            <p>{{ cartItem.name }}</p>
        </li>

        <li v-if="isFullCart"><span class="cart__mobile-title">(Inc VAT)</span>&pound;{{ unitPrice | money }}</li>

        <li><span v-if="!isMiniCart" class="cart__mobile-title">Quantity</span>
            <v-button v-if="!isMiniCart" @click="decreaseQuantity(cartItem)">-</v-button>
                <span class="cart__qty-number">{{ cartItem.quantity }}</span>
            <v-button v-if="!isMiniCart" @click="increaseQuantity(cartItem)">+</v-button>
        </li>

        <li v-if="!isModalCart">
            <span v-if="!isMiniCart" class="cart__mobile-title">Total</span>
            &pound;{{ cartItemTotal(cartItem) }}
        </li>

        <li v-if="isModalCart">
            <span class="cart__mobile-title">Total</span>
            <v-price class="cart__modal-price"
                :amount="cartItemTotal(cartItem)"
                :tax="cartItem.taxRate"
            ></v-price>
        </li>
    </ul>
</template>

<script>
    export default {
        name: 'mx-cart-item',

        props: [
            'item',
            'mini',
            'modal',
        ],

        computed: {
            isMiniCart() {
                return this.mini === true
            },

            isFullCart() {
                return this.mini !== true && this.modal !== true
            },

            isModalCart() {
                return this.modal === true
            },

            cartItem() {
                return this.item
            },

            unitPrice() {
                return this.cartItem.unitPrice
            },

        },

        methods: {
            /**
             * Get the line total for a specific item in the cart
             */
            cartItemTotal() {
                return this.formatMoney(this.cartItem.quantity * this.cartItem.unitPrice)
            },

            /**
             * Broadcast a request to increase the quantity of an item
             */
            increaseQuantity() {
                this.$emit('increase', this.cartItem)
            },

            /**
             * Broadcast a request to decrease the quantity of an item
             */
            decreaseQuantity() {
                this.$emit('decrease', this.cartItem)
            },
        },

    }
</script>
