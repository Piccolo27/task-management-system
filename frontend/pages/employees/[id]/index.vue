<script setup lang="ts">
import {Employee} from "~/types/employee";
import {ADMIN} from "~/constants/constants";

const {$api} = useNuxtApp()
const route = useRoute()
const config = useRuntimeConfig()

const editBtn = {
  type: 'submit',
  class: 'inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white ' +
      'bg-primary-700 rounded-lg focus:ring-4 focus:ring-primary-200 dark:focus:ring-primary-900 hover:bg-primary-800 me-3',
  text: 'Edit'
}

const inputClass = 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 ' +
    'focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 ' +
    'dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500'

const nameInput = {
  type: 'text',
  name: 'name',
  id: 'name',
  class: inputClass,
  disabled: true,
  label: {
    for: 'name',
    text: 'Name',
    isRequired: false
  }
}

const emailInput = {
  type: 'email',
  name: 'email',
  id: 'email',
  class: inputClass,
  disabled: true,
  label: {
    for: 'email',
    text: 'Email',
    isRequired: true
  }
}

const phoneInput = {
  type: 'text',
  name: 'phone',
  id: 'phone',
  class: inputClass,
  disabled: true,
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
  disabled: true,
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
  disabled: true,
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
  type: 'text',
  name: 'position',
  id: 'position',
  class: inputClass,
  disabled: true,
  label: {
    for: 'position',
    text: 'Position',
    isRequired: false
  }
}

const router = useRouter()
const errorMessage = ref<string | null>(null)
const dateOfBirth = ref<Date>(new Date())
const imagePreview = ref<string | ArrayBuffer | null | undefined>(null)

const employee: Employee = reactive({
  name: '',
  email: '',
  position: '',
  address: '',
  phone: '',
  oldPassword: '',
  newPassword: '',
  confirmPassword: ''
})

const employeeId = computed(() => route.params.id)

const label = computed(() => dateOfBirth.value.toLocaleDateString('en-us', {
  year: 'numeric', month: 'numeric', day: 'numeric'
}))

const setEmployee = (data: Employee) => {
  employee.name = data.employee_name
  employee.email = data.email
  imagePreview.value = config.public.storagePath + data.profile
  employee.position = parseInt(data.position as string) == ADMIN ? 'Admin' : 'Member'
  dateOfBirth.value = new Date(data.dob as string)
  employee.address = !!data.address ? data.address : ''
  employee.phone = !!data.phone ? data.phone : ''
}

const setError = (error: Ref<Error | null>) => errorMessage.value = error?.value?.data.error

const { data, error } = await $api.employee.getEmployee(employeeId.value as string)
if (data.value) setEmployee(data.value.employee)
if (error.value) setError(error)

const clearMessages = () => {
  errorMessage.value = null
}

const handleSubmit = () => {
  router.push(`/employees/${employeeId.value}/edit`)
}
</script>

<template>
  <section class="bg-white dark:bg-gray-900">
    <EmployeeForm
      form-title="Employee Profile"
      :button="editBtn"
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
          />
        </div>
        <div class="sm:col-span-2">
          <InputGroup
              v-model="employee.email"
              :input="emailInput"
          />
        </div>
        <div class="sm:col-span-2">
          <img v-if="imagePreview" class="h-auto max-w-xs mb-3" :src="imagePreview" alt="Image Preview">
        </div>
        <div class="sm:col-span-2">
          <InputGroup
              v-model="employee.address"
              :input="addressInput"
          />
        </div>
        <div class="sm:col-span-2">
          <InputGroup
            v-model="employee.phone"
            :input="phoneInput"
          />
        </div>
        <div class="sm:col-span-2">
          <InputGroup
            :input="dobInput"
          >
            <template #date>
              <DatePicker :enable-time-picker="false" v-model="dateOfBirth" :disabled="true" />
            </template>
          </InputGroup>
        </div>
        <div class="sm:col-span-2">
          <InputGroup
            v-model="employee.position"
            :input="positionInput"
          />
        </div>
      </div>
    </EmployeeForm>
  </section>
</template>