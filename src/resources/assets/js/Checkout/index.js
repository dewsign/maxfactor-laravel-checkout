import MxCartItem from './Components/CartItem.vue'
import MxCartDiscount from './Components/CartDiscount.vue'
import MxStripe from './Components/Stripe.vue'

export default {
    install(Vue) {
        Vue.component('mx-cart-item', MxCartItem),
        Vue.component('mx-cart-discount', MxCartDiscount)
        Vue.component('mx-stripe', MxStripe)
    },
}
