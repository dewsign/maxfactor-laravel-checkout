<mx-cart-item
    v-for="cartItem in itemsCollection.all()"
    :class="{ 'cart__item--active' : showMobileCheckoutSummary }"
    :key="cartItem.id"
    :item="cartItem"
    :mini="true"
    dusk="v-cartitem-component"
></mx-cart-item>
