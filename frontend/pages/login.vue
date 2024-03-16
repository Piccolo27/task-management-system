<script setup lang="ts">
import {LoginValidationErrors, LoginInput} from "~/types/auth";
import {useJwt} from "@vueuse/integrations/useJwt";
import ErrorAlert from "~/components/organisms/ErrorAlert.vue";

definePageMeta({
  layout: "non-header-footer"
})

const {$api, $updateToken} = useNuxtApp()
const signInButton = {
  type: 'submit',
  class: 'w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none ' +
      'focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center ' +
      'dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800'
}

const inputClass = 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 ' +
        'focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 ' +
        'dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500'

const emailInput = {
  type: 'email',
  name: 'email',
  placeholder: 'name@example.com',
  class: inputClass,
  label: {
    for: 'email',
    text: 'Email',
    isRequired: true
  }
}

const passwordInput = {
  type: 'password',
  name: 'password',
  placeholder: '••••••••',
  class: inputClass,
  label: {
    for: 'password',
    text: 'Password',
    isRequired: true
  }
}

const isLoading = ref<boolean>(false)
const commonError: Ref<string | null> = ref(null)
const loginInputs: LoginInput = reactive({
  email: '',
  password: ''
})

const validationErrors = reactive<LoginValidationErrors>({
  email: null,
  password: null
});

const login = async () => {
  clearErrors()
  isLoading.value = true

  const {data, error} = await $api.auth.login(loginInputs)

  if (error.value) {
    setErrors(error)
    isLoading.value = false
    return
  }

  if (data.value) {
    useCookie('jwt-token').value = data.value.access_token
    storeUserInCookie(data.value.access_token)
    $updateToken(data.value.access_token)
    isLoading.value = false
  }

  return navigateTo('/')
}

const clearErrors = () => {
  commonError.value = null
  validationErrors.email = null
  validationErrors.password = null
}

const setErrors = (error: Error) => {
  commonError.value = error.value.data?.error ? error.value.data.error : null
  validationErrors.email = error.value.data.errors?.email ? error.value.data.errors.email[0] : null
  validationErrors.password = error.value.data.errors?.password ? error.value.data.errors.password[0] : null
}

const storeUserInCookie = (jwtToken: string) => {
  const encodedJwt = ref(jwtToken)
  const {payload} = useJwt(encodedJwt)

  const user = {
    'id': payload.value?.id,
    'name': payload.value?.name,
    'email': payload.value?.email,
    'profile': payload.value?.profile,
    'position': payload.value?.position
  }

  useCookie('user').value = JSON.stringify(user)
}
</script>

<template>
  <section class="bg-gray-50 dark:bg-gray-900">
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
      <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
        Task Management System
      </a>
      <div
        v-if="commonError"
        class="w-full md:mt-0
         sm:max-w-md xl:p-0"
      >
        <ErrorAlert @dismiss="clearErrors">{{ commonError }}</ErrorAlert>
      </div>
      <AuthForm form-title="Login">
        <form class="space-y-4 md:space-y-6" @submit.prevent="login">
          <div>
            <InputGroup
              v-model="loginInputs.email"
              :input="emailInput"
              :error-message="validationErrors.email"
            />
          </div>
          <div>
            <InputGroup
              v-model="loginInputs.password"
              :input="passwordInput"
              :error-message="validationErrors.password"
            />
          </div>
          <div class="flex items-center justify-between">
            <NuxtLink
              to="/forgot-password"
              class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500"
            >
              Forgot password?
            </NuxtLink>
          </div>
          <BtnWithSpinner :button="signInButton" :disabled="isLoading">
            Sign in
          </BtnWithSpinner>
        </form>
      </AuthForm>
    </div>
  </section>
</template>