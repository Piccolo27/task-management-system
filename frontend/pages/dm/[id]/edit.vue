<script setup lang="ts">
import {initModals} from "flowbite";
import {Employee} from "~/types/employee";
import {AuthUser} from "~/types/auth";
import {DmCreateValidationErrors} from "~/types/dm";
import {REPLYABLE} from "~/constants/constants";
import {useStorage} from "@vueuse/core";

const user = useCookie('user').value as any as AuthUser
const {$api} = useNuxtApp()
const route = useRoute()
const editBtn = {
  type: 'submit',
  class: 'inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-primary-700 ' +
      'rounded-lg focus:ring-4 focus:ring-primary-200 dark:focus:ring-primary-900 hover:bg-primary-800 me-3'
}

const isLoading = ref(false)
const errorMessage = ref<string | null>(null)
const reservationAndTransmissionDT = ref<Date|null>(null)
const selectedEmployees = ref<Employee[]>([])
const directMessage = reactive({
  title: '',
  text: '',
  replyable: false,
  owner: {} as Employee
})

const validationErrors = reactive<DmCreateValidationErrors>({
  title: '',
  text: '',
  selectedPerson: '',
  reservationAndTransmissionDT: ''
})

const dmID = computed(() => route.params.id)

const { data, error } = await $api.dm.get(dmID.value as string)
if (error.value) errorMessage.value = error.value.data.error
if (data.value) {
  directMessage.title = data.value.dm[0].title
  directMessage.text = data.value.dm[0].body
  directMessage.replyable = data.value.dm[0].replyable == REPLYABLE
  directMessage.owner = data.value.dm[0].owner
  selectedEmployees.value = data.value.dm[0].dm_thread.members
  reservationAndTransmissionDT.value = new Date(data.value.dm[0].start_at)
}

const handleSelectedEmployees = (dataFromChild: Employee[]) => {
  selectedEmployees.value = dataFromChild
}

const handleSubmit = async() => {
  clearMessages()
  isLoading.value = true
  const dmData = getFormData()
  const { data, error } = await $api.dm.update(dmData, dmID.value as string)

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
  }
}

const clearMessages = () => {
  for (const field of Object.keys(validationErrors)) {
    (validationErrors as any)[field] = null
  }
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
    <div class="py-8 px-4 mx-auto max-w-2xl lg:py-16">
      <h2 class="mb-12 text-xl font-bold text-gray-900 dark:text-white text-center">DM new creation</h2>
      <form @submit.prevent="handleSubmit">
        <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
          <div class="sm:col-span-2">
            <label for="target-person" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
              Target person selection<span class="text-red-700">*</span>
            </label>
            <button type="button" class="text-gray-900 bg-white hover:bg-gray-100 border border-gray-200
                focus:ring-4 focus:outline-none focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5
                text-center inline-flex items-center dark:focus:ring-gray-600 dark:bg-gray-800 dark:border-gray-700
                dark:text-white dark:hover:bg-gray-700 mr-2 mb-2"
                    data-modal-target="target-person-selection-modal" data-modal-toggle="target-person-selection-modal">
              Destination selection
            </button>
            <p v-if="validationErrors.selectedPerson" class="mt-2 text-xs text-red-600 dark:text-red-400">
              <span class="font-medium">{{ validationErrors.selectedPerson }}</span>
            </p>
          </div>
          <div class="sm:col-span-2">
            <label for="dm-sender" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
              DM sender selection<span class="text-red-700">*</span>
            </label>
            <button type="button" class="text-gray-900 bg-white border border-gray-200
                font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center
                dark:bg-gray-800 dark:border-gray-700 dark:text-white mr-2 mb-2 cursor-not-allowed" disabled>
              {{ directMessage.owner.employee_name }}
            </button>
          </div>
          <div class="sm:col-span-2">
            <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
              Title<span class="text-red-700">*</span>
            </label>
            <input type="text" name="title" id="title" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm
                rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700
                dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500
                dark:focus:border-primary-500" v-model="directMessage.title">
            <p v-if="validationErrors.title" class="mt-2 text-xs text-red-600 dark:text-red-400">
              <span class="font-medium">{{ validationErrors.title }}</span>
            </p>
          </div>
          <div class="sm:col-span-2">
            <label for="text" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
              Text<span class="text-red-700">*</span>
            </label>
            <textarea id="tezt" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border
                border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600
                dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                      v-model="directMessage.text"
            ></textarea>
            <p v-if="validationErrors.text" class="mt-2 text-xs text-red-600 dark:text-red-400">
              <span class="font-medium">{{ validationErrors.text }}</span>
            </p>
          </div>
          <div class="sm:col-span-2">
            <label for="datetime" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
              Reservation transmission date and time
            </label>
            <div class="flex items-center">
              <DatePicker
                  v-model="reservationAndTransmissionDT"
                  @change-date-time="changeReservationAndTransmissionDT"
                  :enable-time-picker="true"
              />
            </div>
            <p v-if="validationErrors.reservationAndTransmissionDT" class="mt-2 text-xs text-red-600 dark:text-red-400">
              <span class="font-medium">{{ validationErrors.reservationAndTransmissionDT }}</span>
            </p>
          </div>
          <div class="sm:col-span-2">
            <label for="replyable" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
              Replyable
            </label>
            <input
                id="replyable"
                type="checkbox"
                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500
                  dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                v-model="directMessage.replyable"
            />
          </div>
        </div>
        <BtnWithSpinner :button="editBtn" :disabled="isLoading">
          Update
        </BtnWithSpinner>
        <NuxtLink to="/dm/list" type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4
           focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-gray-800
            dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700" >
          Cancel
        </NuxtLink>
      </form>
    </div>
  </section>
  <TargetPersonSelectionModal @selected-employees="handleSelectedEmployees" :oldSelectedEmployees="selectedEmployees"/>
</template>