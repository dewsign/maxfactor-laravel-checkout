import MxCartItem from './Components/CartItem.vue'
import MxCartDiscount from './Components/CartDiscount.vue'

export default {
    install(Vue) {
        Vue.component(MxCartItem),
        Vue.component('mx-cart-discount', MxCartDiscount)
    },
}
