import MxCartItem from './Components/CartItem.vue'
import MxCartDiscount from './Components/CartDiscount.vue'
import MxStripe from './Components/Stripe.vue'
import MxAddress from './Components/AddressLookup.vue'
import MxDelivery from './Components/DeliverySelector.vue'
import DeliveryMonth from './Components/Delivery/DeliveryMonth.vue'
import DeliveryWeek from './Components/Delivery/DeliveryWeek.vue'
import DeliveryOption from './Components/Delivery/DeliveryOption.vue'

export default {
    install(Vue) {
        Vue.component('mx-cart-item', MxCartItem)
        Vue.component('mx-cart-discount', MxCartDiscount)
        Vue.component('mx-stripe', MxStripe)
        Vue.component('mx-address', MxAddress)
        Vue.component('mx-delivery', MxDelivery)
        Vue.component('delivery-month', DeliveryMonth)
        Vue.component('delivery-week', DeliveryWeek)
        Vue.component('delivery-option', DeliveryOption)
    },
}
