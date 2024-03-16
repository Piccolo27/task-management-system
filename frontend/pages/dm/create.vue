<script setup lang="ts">
import {initModals} from "flowbite";
import {Employee} from "~/types/employee";
import {AuthUser} from "~/types/auth";
import {DmCreateValidationErrors} from "~/types/dm";
import {useStorage} from "@vueuse/core";

const user = useCookie('user').value as any as AuthUser
const {$api} = useNuxtApp()
const createBtn = {
  type: 'submit',
  class: 'inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-primary-700 ' +
      'rounded-lg focus:ring-4 focus:ring-primary-200 dark:focus:ring-primary-900 hover:bg-primary-800 me-3',
  text: 'Save'
}

const inputClass = 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 ' +
    'focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 ' +
    'dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500'

const inputs = {
  targetPersonSelectBtn: {
    type: 'targetPersonSelectBtn',
    name: 'target_person_select_btn',
    id: 'target_person_select_btn',
    class: '',
    label: {
      for: 'target_person_select_btn',
      text: 'Target person selection',
      isRequired: true
    }
  },
  disabledBtn: {
    type: 'disabledBtn',
    name: 'sender',
    id: 'sender',
    class: '',
    label: {
      for: 'sender',
      text: 'DM sender selection',
      isRequired: true
    }
  },
  title: {
    type: 'text',
    name: 'title',
    id: 'title',
    class: inputClass,
    label: {
      for: 'title',
      text: 'DM Title',
      isRequired: true
    }
  },
  text: {
    type: 'textarea',
    name: 'text',
    id: 'text',
    rows: 4,
    class: 'block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 ' +
        'focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white ' +
        'dark:focus:ring-blue-500 dark:focus:border-blue-500',
    label: {
      for: 'text',
      text: 'DM Text',
      isRequired: true
    }
  },
  reservationAndTransmissionDTInput: {
    type: 'date',
    name: 'raDT',
    id: 'raDT',
    label: {
      for: 'raDT',
      text: 'Reservation transmission date and time',
      isRequired: false
    }
  },
  replyable: {
    type: 'checkbox',
    name: 'replyable',
    id: 'replyable',
    class: 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 ' +
        'dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 v-model="directMessage.replyable',
    label: {
      for: 'replyable',
      text: 'Replyable',
      isRequired: false
    }
  }
}

const isLoading = ref(false)
const errorMessage = ref('')
const reservationAndTransmissionDT = ref<Date|null>(null)
const selectedEmployees = ref<Employee[]>([])
const directMessage = reactive({
  title: '',
  text: '',
  replyable: false
})

const validationErrors = reactive<DmCreateValidationErrors>({
  title: '',
  text: '',
  selectedPerson: '',
  reservationAndTransmissionDT: ''
})

const handleSelectedEmployees = (dataFromChild: Employee[]) => {
  selectedEmployees.value = dataFromChild
}

const handleSubmit = async() => {
  clearMessages()
  isLoading.value = true
  const dmData = getFormData()
  const { data, error } = await $api.dm.create(dmData)

  if (error.value) {
  isLoading.value = false

    setErrorMessages(error.value)
  }

  if (data.value) {
    useStorage('employee-success', data.value.message, sessionStorage)
    isLoading.value = false
    return navigateTo('/dm/list')
  }
}

const getFormData = () => {
  return {
    title: directMessage.title,
    text: directMessage.text,
    replyable: directMessage.replyable,
    selectedPerson: selectedEmployees.value.map(e => e.employee_id) as string[],
    reservationAndTransmissionDT: reservationAndTransmissionDT.value !== null 
      ? toLocalISOString(reservationAndTransmissionDT.value)
      : null
  }
}

const setErrorMessages = (error: Error) => {
  if (error.statusCode === 422) {
    for (const key of Object.keys(error.data.errors)) {
      if (error.data.errors.hasOwnProperty(key)) {
        (validationErrors as any)[key] = error.data.errors[key][0];
      }
    }
  } else {
    errorMessage.value = error.data.error.message
  }
}

const clearMessages = () => {
  for (const field of Object.keys(validationErrors)) {
    (validationErrors as any)[field] = null
  }

  errorMessage.value = ''
}

const changeReservationAndTransmissionDT = (newState: Date) => reservationAndTransmissionDT.value = newState

const toLocalISOString = (date: Date) => {
  const offset = date.getTimezoneOffset();
  const adjustedDate = new Date(date.getTime() - offset * 60000);
  return adjustedDate.toISOString().split('T')[0] + ' ' + date.toTimeString().split(' ')[0];
}

onMounted(() => {
  initModals()
})
</script>

<template>
    <section class="bg-white dark:bg-gray-900">
      <DmForm
        form-title="DM new creation"
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
                :input="inputs.targetPersonSelectBtn"
                :error-message="validationErrors.selectedPerson"
            >
              <template #targetPersonSelect>
                <button
                  type="button"
                  class="text-gray-900 bg-white hover:bg-gray-100 border border-gray-200
                    focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5
                    text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700
                    dark:text-white dark:hover:bg-gray-700 mr-2 mb-2"
                  data-modal-target="target-person-selection-modal"
                  data-modal-toggle="target-person-selection-modal"
                >
                  Destination selection
                </button>
              </template>
            </InputGroup>
          </div>
          <div class="sm:col-span-2">
            <InputGroup
                :input="inputs.disabledBtn"
            >
              <template #disabledButton>
                <button
                    type="button"
                    class="text-gray-900 bg-white border border-gray-200
                      font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center
                      dark:bg-gray-800 dark:border-gray-700 dark:text-white mr-2 mb-2 cursor-not-allowed"
                    disabled
                >
                  {{ user.name }}
                </button>
              </template>
            </InputGroup>
          </div>
          <div class="sm:col-span-2">
            <InputGroup
                v-model="directMessage.title"
                :input="inputs.title"
                :error-message="validationErrors.title"
            />
          </div>
          <div class="sm:col-span-2">
            <InputGroup
                v-model="directMessage.text"
                :input="inputs.text"
                :error-message="validationErrors.text"
            />
          </div>
          <div class="sm:col-span-2">
            <InputGroup
                :input="inputs.reservationAndTransmissionDTInput"
                :error-message="validationErrors.reservationAndTransmissionDT"
            >
              <template #date>
                <DatePicker
                    @change-date-time="changeReservationAndTransmissionDT"
                    :enable-time-picker="true"
                />
              </template>
            </InputGroup>
          </div>
          <div class="sm:col-span-2">
            <InputGroup
                v-model="directMessage.replyable"
                :input="inputs.replyable"
            />
          </div>
        </div>
      </DmForm>
    </section>
    <TargetPersonSelectionModal @selected-employees="handleSelectedEmployees"/>
  </template>