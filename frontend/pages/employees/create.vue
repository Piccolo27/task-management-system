<script setup lang="ts">
import {EmployeeValidationError} from "~/types/employee";
import {useStorage} from "@vueuse/core";

const {$api} = useNuxtApp()

const createBtn = {
  type: 'submit',
  class: 'inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white ' +
      'bg-primary-700 rounded-lg focus:ring-4 focus:ring-primary-200 dark:focus:ring-primary-900 hover:bg-primary-800 me-3',
  text: 'Save'
}

const inputClass = 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 ' +
    'focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 ' +
    'dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500'

const nameInput = {
  type: 'text',
  name: 'name',
  id: 'name',
  class: inputClass,
  label: {
    for: 'name',
    text: 'Name',
    isRequired: true
  }
}

const emailInput = {
  type: 'email',
  name: 'email',
  id: 'email',
  class: inputClass,
  label: {
    for: 'email',
    text: 'Email',
    isRequired: true
  }
}

const fileInput = {
  type: 'file',
  name: 'profile',
  id: 'profile',
  class: 'block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 ' +
      'dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400',
  label: {
    for: 'profile',
    text: 'Profile Photo',
    isRequired: true
  }
}

const phoneInput = {
  type: 'text',
  name: 'phone',
  id: 'phone',
  class: inputClass,
  label: {
    for: 'phone',
    text: 'Phone',
    isRequired: false
  }
}

const dobInput = {
  type: 'date',
  name: 'dob',
  id: 'dob',
  label: {
    for: 'dob',
    text: 'DOB',
    isRequired: false
  }
}

const addressInput = {
  type: 'textarea',
  name: 'address',
  id: 'address',
  rows: 4,
  class: 'block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 ' +
      'focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white ' +
      'dark:focus:ring-blue-500 dark:focus:border-blue-500',
  label: {
    for: 'address',
    text: 'Address',
    isRequired: false
  }
}

const positionInput = {
  type: 'select',
  name: 'position',
  id: 'position',
  class: 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 ' +
      'block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white ' +
      'dark:focus:ring-blue-500 dark:focus:border-blue-500',
  label: {
    for: 'position',
    text: 'Position',
    isRequired: true
  },
  selectOptions: [{0: 'admin'}, {1: 'member'}]
}

const dateOfBirth = ref<Date | null>(new Date())
const profileImage = ref<File | null>(null)
const imagePreview = ref<string | ArrayBuffer | null | undefined>(null)
const isLoading = ref<boolean>(false)

const employee = reactive({
  name: '',
  email: '',
  address: '',
  phone: '',
  position: ''
})

const errorMessage = ref<string | null>(null)
const validationErrors = reactive<EmployeeValidationError>({
  employee_name: null,
  email: null,
  profile: null,
  address: null,
  phone: null,
  position: null,
  dob: null
})

const previewImage = (file: File) => {
  const reader = new FileReader()
  reader.onload = (e) => imagePreview.value = e?.target?.result
  reader.readAsDataURL(file)
}

const handleSubmit = async () => {
  isLoading.value = true
  clearMessages()

  const formData = getFormData()
  const {data, error} = await $api.employee.create(formData)

  if (error.value) {
    setErrorMessages(error.value)
    isLoading.value = false
    return
  }

  if (data.value) {
    useStorage('employee-success', data.value.message, sessionStorage)
    isLoading.value = false
    return navigateTo('/employees/list')
  }
}

const getFormData = () => {
  const profile: Blob | string = !!(profileImage.value) ? profileImage.value as Blob : ''

  const body = new FormData()
  body.append('employee_name', employee.name)
  body.append('email', employee.email)
  body.append('profile', profile as Blob)
  body.append('address', employee.address)
  body.append('phone', employee.phone)
  body.append('position', employee.position)
  body.append('dob', getDobAsString(dateOfBirth.value))

  return body
}

const getDobAsString = (dobObject: Date): string => {
  return dobObject.toISOString().split('T')[0]
}

const setErrorMessages = (error: Error) => {
  if (error.statusCode === 422) {
    for (const key of Object.keys(error.data.errors)) {
      if (error.data.errors.hasOwnProperty(key)) {
        const value: string = error.data.errors[key][0];
        (validationErrors as any)[key] = value;
      }
    }
  }

  if (error.statusCode === 500) {
    errorMessage.value = error.data.error
  }
}

const clearMessages = () => {
  errorMessage.value = null
  for (const field of Object.keys(validationErrors)) {
    (validationErrors as any)[field] = null
  }
}

const changeDateOfBirth = (newState: Date) => dateOfBirth.value = newState

watch(profileImage, () => {
  previewImage(profileImage.value as File)
})
</script>

<template>
  <section class="bg-white dark:bg-gray-900">
    <EmployeeForm
      form-title="New Employee"
      :button="createBtn"
      :btn-disabled="isLoading"
      @form-submit="handleSubmit"
    >
      <template #errorNoti>
        <ErrorAlert v-if="errorMessage" @dismiss="clearMessages">{{ errorMessage }}</ErrorAlert>
      </template>
      <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
        <div class="sm:col-span-2">
          <InputGroup
              v-model="employee.name"
              :input="nameInput"
              :error-message="validationErrors.employee_name"
          />
        </div>
        <div class="sm:col-span-2">
          <InputGroup
              v-model="employee.email"
              :input="emailInput"
              :error-message="validationErrors.email"
          />
        </div>
        <div class="sm:col-span-2">
          <InputGroup
              v-model="profileImage"
              :input="fileInput"
              :error-message="validationErrors.profile"
          >
            <template #previewImage>
              <img v-if="imagePreview" class="h-auto max-w-xs mb-3" :src="imagePreview" alt="Image Preview">
            </template>
          </InputGroup>
        </div>
        <div class="sm:col-span-2">
          <InputGroup
              v-model="employee.address"
              :input="addressInput"
              :error-message="validationErrors.address"
          />
        </div>
        <div class="sm:col-span-2">
          <InputGroup
              v-model="employee.phone"
              :input="phoneInput"
              :error-message="validationErrors.phone"
          />
        </div>
        <div class="sm:col-span-2">
          <InputGroup
              :input="dobInput"
              :error-message="validationErrors.dob"
          >
            <template #date>
              <DatePicker @change-date-time="changeDateOfBirth" :enable-time-picker="false" />
            </template>
          </InputGroup>
        </div>
        <div class="sm:col-span-2">
          <InputGroup
              v-model="employee.position"
              :input="positionInput"
              :error-message="validationErrors.position"
          />
        </div>
      </div>
    </EmployeeForm>
  </section>
</template>