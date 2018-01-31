<div class="checkout__header">
    <header class="header header--checkout" :class="headerClass">
        <div class="header__content">
            @include('maxfactor::checkout.components.brand')
        </div>
    </header>
    <button name="button" class="checkout__summary-toggle" @click.prevent="toggleMobileCheckoutSummary()" :class="{ 'checkout__summary-toggle--active' : showMobileCheckoutSummary }">
        <h4>@lang('Show order summary')<span>&pound;@{{ cartSubTotal | money }}</span></h4>
    </button>
</div>
