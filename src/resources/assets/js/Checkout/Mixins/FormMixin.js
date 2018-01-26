export default {
    data() {
        return {
            form: {
                errors: {},
                loading: false,
                sender: window.axios.create(),
                status: null,
            },
        }
    },

    watch: {
        formIsLoading: {
            handler(status) {
                if (status === true) {
                    document.body.classList.add('form-is-loading')
                } else {
                    document.body.classList.remove('form-is-loading')
                }
            },
        },
    },

    computed: {
        formIsLoading: {
            get() {
                return this.form.loading
            },
            set(value) {
                this.form.loading = value
            },
        },

        formHasErrors: {
            get() {
                return Object.keys(this.form.errors).length > 0
            },
        },

        formClass: {
            get() {
                return {
                    'form-is-available': !this.formIsLoading,
                }
            },
        },
    },

    methods: {
        setFormUnavailable() {
            this.formIsLoading = true
        },

        setFormAvailable() {
            this.formIsLoading = false
        },

        isFormValid(formRef = 'form') {
            if (!this.$refs[formRef]) return true

            return this.$refs[formRef].validate()
        },

        clearFormErrors() {
            this.$set(this.form, 'errors', {})
        },

        setFormErrors(response) {
            if (!response) return

            this.$set(this.form, 'errors', response.data.errors)
        },

        formError(field) {
            if (!this.form.errors) return false

            if (typeof this.form.errors[field] === 'string') return this.form.errors[field]
            if (typeof this.form.errors[field] === 'undefined') return ''

            return this.form.errors[field][0]
        },

        setFormStatus(response) {
            if (!response.status) return

            this.$set(this.form, 'status', response.status)
        },

        getForm(formTarget, formData, formRef = 'form') {
            this.clearFormErrors()

            if (!this.isFormValid(formRef)) return false

            this.setFormUnavailable()

            return this.form.sender.get(formTarget, formData)
        },

        postForm(formTarget, formData, formRef = 'form') {
            this.clearFormErrors()

            if (!this.isFormValid(formRef)) return false

            this.setFormUnavailable()

            return this.form.sender.post(formTarget, formData)
        },

        deleteForm(formTarget, formData, formRef = 'form') {
            this.clearFormErrors()

            if (!this.isFormValid(formRef)) return false

            this.setFormUnavailable()

            return this.form.sender.delete(formTarget, formData)
        },
    },

    created() {
        /**
         * Hook into axios requests to set the form loading status and check for errors
         */
        this.form.sender.interceptors.request.use((config) => {
            this.setFormUnavailable()

            return Promise.resolve(config)
        }, (error) => {
            this.setFormAvailable()
            this.setFormStatus(error.response)
            this.setFormErrors(error.response)

            return Promise.reject(error)
        })

        /**
         * Hook into the axios response to set the form loading status and check for errors
         */
        this.form.sender.interceptors.response.use((event) => {
            this.setFormAvailable()
            this.setFormErrors(event.response)

            return Promise.resolve(event)
        }, (error) => {
            this.setFormAvailable()
            this.setFormStatus(error.response)
            this.setFormErrors(error.response)

            return Promise.reject(error)
        })
    },
}
