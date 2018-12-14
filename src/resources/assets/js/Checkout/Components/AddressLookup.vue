<template>
    <div class="checkout__customer-info">

        <div class="user__field user__field--half sort-order1" v-if="!ui.showAddress">
            <label for="addressPostcode" class="label--required">Post code</label>
            <input id="addressPostcode" type="text" v-model="address.address_postcode" required>
            <v-form-error field="address_postcode"></v-form-error>
        </div>

        <div class="user__field user__field--half sort-order2" v-if="!ui.showAddress">
            <button
                class="button button--brand-2"
                @click.prevent="lookupPostcode"
            >Lookup</button>
            <button
                @click.prevent="ui.showAddress = true"
            >Enter address manually</button>
        </div>

        <slot
            name="selection"
            :addresses="addresses"
            :haveAddresses="haveAddresses"
            :select="selectAddress"
            :clearAddresses="clearAddresses"
            v-if="haveAddresses && !ui.showAddress"
        >
            {{ addresses }}
        </slot>

        <slot
            :address="address"
            :changeAddress="changeAddress"
            v-if="ui.showAddress"
        >
            Postcode form
        </slot>

    </div>
</template>

<script>
    import { Lookup } from '@thesold/pcapredict'

    export default {

        name: 'AddressLookup',

        props: {

            /**
             * The main v-model to use for the address data
             */
            value: {
                type: Object,
                required: true,
            },

            /**
             * A valid PCA Predict API key. If no API key is supplied this components will act like
             * a basic address entry.
             */
            apiKey: {
                type: String,
                required: false,
                default: null,
            },

        },

        data() {
            return {
                lookup: null, // Store a reference to our postcode lookup object
                address: this.value,
                addresses: [],
                ui: {
                    showAddress: false, // Shows the full address when true
                },
            }
        },

        computed: {
            haveAddress() {
                return this.address.address
                    && this.address.address_city
                    && this.address.address_postcode
            },

            haveAddresses() {
                return this.addresses.length > 0
            },
        },

        watch: {
            value: {
                handler(newValue) {
                    this.$set(this, 'address', newValue)
                },
                deep: true,
            },
        },

        methods: {
            /**
             * Lookup a postcode and return a list of available addresses
             */
            lookupPostcode() {
                this.lookup.clear().find(this.address.address_postcode).get()
                    .then((results) => {
                        this.$set(this, 'addresses', results)
                        this.$set(this.form.errors, 'address_postcode', [])
                    })
                    .catch(() => {
                        this.$set(this.form.errors, 'address_postcode', ['Postcode invalid'])
                    })
            },

            changeAddress() {
                this.clearAddresses()
                this.ui.showAddress = false
            },

            clearAddresses() {
                this.$set(this, 'addresses', [])
                this.lookup.clear()
            },

            /**
             * Scoped-prop to call when an address has been selected. Retrieves the full address
             * and populates the address Objects with the details.
             */
            selectAddress(address) {
                this.lookup.retrieve(address).then((fullAddress) => {
                    this.populateCustomerAddress(fullAddress)
                })
            },

            populateCustomerAddress(address) {
                this.$set(this.value, 'company', address.Company)
                this.$set(this.value, 'address', address.Line1)
                this.$set(this.value, 'address_2', address.Line2)
                this.$set(this.value, 'address_3', address.Line3)
                this.$set(this.value, 'address_city', address.City)
                this.$set(this.value, 'address_postcode', address.PostalCode)
                this.$set(this.value, 'address_county', address.Province)

                this.ui.showAddress = true
            },
        },

        created() {
            /**
             * Disable the post code search if no Api key is supplied
             */
            if (!this.apiKey) {
                this.ui.showAddress = true
                return
            }

            this.lookup = new Lookup(this.apiKey)
        },

        mounted() {
            /**
             * Show the full address if we already have the details when the component is mounted
             */
            if (this.haveAddress) this.ui.showAddress = true

            /**
             * Show the full address if we are returning from paypal
             */
            if (this.activeCartCollection.payment.provider) this.ui.showAddress = true
        },

    }
</script>
