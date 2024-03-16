<script setup lang="ts">
import SuccessAlert from "~/components/organisms/SuccessAlert.vue";

definePageMeta({
  layout: "non-header-footer"
})

const {$api} = useNuxtApp()

const emailInput = {
  type: 'email',
  name: 'email',
  required: true,
  placeholder: 'name@example.com',
  class: 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 ' +
      'focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 ' +
      'dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
  label: {
    for: 'email',
    text: 'Email',
    isRequired: true
  }
}

const forgotPasswordBtn = {
  type: 'submit',
  class: 'w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none ' +
      'focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 ' +
      'dark:hover:bg-primary-700 dark:focus:ring-primary-800'
}

const email = ref('')
const successMessage = ref<string | null>(null)
const emailValidationError = ref<string | null>(null)
const isLoading = ref<boolean>(false)

const handleSubmit = async () => {
  isLoading.value = true
  const {data, error} = await $api.auth.forgotPassword(email.value)

  if (error.value) {
    emailValidationError.value = error.value.data.errors?.email[0]
    isLoading.value = false
    return
  }

  if (data.value) {
    successMessage.value = data.value.message
    isLoading.value = false
  }
}

const clearMessage = () => successMessage.value = null
</script>

<template>
  <section class="bg-gray-50 dark:bg-gray-900">
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
      <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
        Task Management System
      </a>
      <div
        v-if="successMessage"
        class="w-full md:mt-0 sm:max-w-md xl:p-0"
      >
        <SuccessAlert @dismiss="clearMessage">{{ successMessage }}</SuccessAlert>
      </div>
      <AuthForm form-title="Forgot Password">
        <form class="mt-4 space-y-4 lg:mt-5 md:space-y-5" @submit.prevent="handleSubmit">
          <div>
            <InputGroup
              v-model="email"
              :input="emailInput"
              :error-message="emailValidationError"
            />
          </div>
          <BtnWithSpinner
            :button="forgotPasswordBtn"
            :disabled="isLoading"
          >
            Send
          </BtnWithSpinner>
        </form>
      </AuthForm>
    </div>
  </section>
</template>