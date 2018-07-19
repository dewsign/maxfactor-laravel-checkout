<div class="generic__continue">
    <a class="checkout__return-cart" href="{{ $returnUrl }}">< {{ $returnLabel }}</a>
    <button 
        data-url="{{ $continueUrl }}" 
        class="btn-primary btn--pre-loading"
        :class="formIsLoading ? 'btn--loading' : ''"
        @click.prevent="{{ isset($onClick) ? $onClick : 'prepareCheckout' }}"
        :disabled="formIsLoading === true"
    >{{ $continueLabel }}</button>

    <p class="error" v-if="formHasErrors">Please review required fields</p>
</div>
