<script setup lang="ts">
import {ResetPasswordInput, ResetPasswordValidationErrors} from "~/types/auth";
import ErrorAlert from "~/components/organisms/ErrorAlert.vue";
import SuccessAlert from "~/components/organisms/SuccessAlert.vue";
import Spinner from "~/components/atoms/Spinner.vue";

definePageMeta({
  layout: "non-header-footer"
})

const {$api} = useNuxtApp()
const route = useRoute()

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

const passwordInput = {
  type: 'password',
  name: 'password',
  required: true,
  placeholder: '••••••••',
  class: 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 ' +
      'focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 ' +
      'dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
  label: {
    for: 'password',
    text: 'Password',
    isRequired: true
  }
}

const resetPasswordBtn = {
  type: 'submit',
  class: 'w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 ' +
      'font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-60 dark:hover:bg-primary-700 ' +
      'dark:focus:ring-primary-800'
}

const isLoading = ref<boolean>(false)
const successMessage = ref<string | null>(null)
const validationErrors = reactive<ResetPasswordValidationErrors>({
  email: null,
  password: null,
  password_confirmation: null,
})

const formInputs = reactive<ResetPasswordInput>({
  email: '',
  password: '',
  password_confirmation: '',
  token: route.query.token
})

const resetPassword = async () => {
  isLoading.value = true
  const {data, error} = await $api.auth.resetPassword(formInputs)

  if (error.value) {
    setErrors(error.value)
    isLoading.value = false
    return
  }

  if (data.value) {
    successMessage.value = data.value.message
    isLoading.value = false
    setTimeout(() => {
      clearMessages()
      return navigateTo('/login')
    }, 2000)
  }
}

const setErrors = (error: Error) => {
  validationErrors.email = error.data?.error ? error.data.error : null
  validationErrors.password = error.data.errors?.email ? error.data.errors.email[0] : null
  validationErrors.password_confirmation = error.data.errors?.password_confirmation
      ? error.data.errors.password_confirmation[0]
      : null
}

const clearMessages = () => {
  successMessage.value = null
  validationErrors.email = null
  validationErrors.password = null
  validationErrors.password_confirmation = null
}
</script>

<template>
  <section class="bg-gray-50 dark:bg-gray-900">
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
      <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
        Task Management System
      </a>
      <div
        v-if="successMessage"
        class="w-full md:mt-0
          sm:max-w-md xl:p-0"
      >
        <SuccessAlert @dismiss="clearMessages">{{ successMessage }}</SuccessAlert>
      </div>
      <AuthForm form-title="Reset Password">
        <form class="mt-4 space-y-4 lg:mt-5 md:space-y-5" @submit.prevent="resetPassword">
          <div>
            <InputGroup
              v-model="formInputs.email"
              :input="emailInput"
              :error-message="validationErrors.email"
            />
          </div>
          <div>
            <InputGroup
              v-model="formInputs.password"
              :input="passwordInput"
              :error-message="validationErrors.password"
            />
          </div>
          <div>
            <label for="confirm-password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
              Confirm password <span class="text-red-700">*</span>
            </label>
            <InputGroup
              v-model="formInputs.password_confirmation"
              :input="passwordInput"
              :error-message="validationErrors.password_confirmation"
            />
          </div>
          <BtnWithSpinner
            :button="resetPasswordBtn"
            :disabled="isLoading"
          >
            Confirm
          </BtnWithSpinner>
        </form>
      </AuthForm>
    </div>
  </section>
</template>