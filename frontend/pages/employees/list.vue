<script setup lang="ts">
import {useStorage} from '@vueuse/core';
import {Employee} from '~/types/employee';
import SearchForm from "~/components/organisms/SearchForm.vue";

const {getSessionStorage, clearSessionStorage, compareStrings, compareNumbers, compareDates} = useUtils()
const {$api} = useNuxtApp()
const config = useRuntimeConfig()

const inputClass = 'block p-2 text-sm text-gray-900 border border-gray-300 rounded-lg w-60 bg-gray-50 focus:ring-blue-500 ' +
    'focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white ' +
    'dark:focus:ring-blue-500 dark:focus:border-blue-500'

const idSearchInput = {
  name: 'id_search',
  type: 'number',
  class: inputClass
}

const nameSearchInput = {
  name: 'name_search',
  type: 'text',
  class: inputClass
}

const storagePath = config.public.storagePath
const successMessage = ref<string | null>(null)
const employees = ref<Employee[]>([])
const page = ref(1)
const employeesPerPage = 5
const searchQueryByID = ref('')
const searchQueryByName = ref('')
const sortingOrder = ref({field: 'employee_id', direction: 'asc'})
const errorMessage = ref<string | null>(null)

const paginatedEmployees: ComputedRef<Employee[]> = computed(() => {
  const startIndex = (page.value - 1) * employeesPerPage
  const endIndex = startIndex + employeesPerPage
  const filteredEmployees = employees.value.filter(checkIdAndName)
  const sortFunction = getSortFunction()
  const sortedEmployees = filteredEmployees.sort(sortFunction)

  return sortedEmployees.slice(startIndex, endIndex)
})

const checkIdAndName = (employee: Employee) => {
  const matchId = !searchQueryByID.value || employee.employee_id == searchQueryByID.value;
  const matchName = !searchQueryByName.value || (employee.employee_name as string).toLowerCase().includes(searchQueryByName.value.toLowerCase());

  return matchId && matchName;
}

const getSortFunction = () => {
  const field = sortingOrder.value.field
  const direction = sortingOrder.value.direction
  const compare = fieldTypes[field] || compareNumbers

  if (direction === 'asc') {
    return (a: any, b: any) => compare(a[field], b[field]);
  } else {
    return (a: any, b: any) => compare(b[field], a[field]);
  }
}

const fieldTypes: any = {
  employee_id: compareNumbers,
  employee_name: compareStrings,
  dob: compareDates
}

const clearSearchQueries = () => {
  searchQueryByID.value = ''
  searchQueryByName.value = ''
}

const changeSortingOrderByField = (field: string) => {
  const direction = sortingOrder.value.direction === 'asc' ? 'desc' : 'asc'
  sortingOrder.value.field = field
  sortingOrder.value.direction = direction
}

const getEmployees = async () => {
  const {data, error} = await $api.employee.getEmployees()
  if (data.value) {
    employees.value = data.value.employees
  }

  if (error.value) {
    errorMessage.value = error.value.data.error
  }
}

await getEmployees()

const clearSuccessMessage = () => {
  clearSessionStorage('employee-success')
  successMessage.value = null
}

const deleteEmployee = async (employeeId: string) => {
  const employeeDeleteConfirm = window.confirm("Are you sure to delete?")

  if (employeeDeleteConfirm) {
    const {data, error} = await $api.employee.delete(employeeId)

    if (error.value) errorMessage.value = error.value.data.error

    if (data.value) {
      useStorage('employee-success', data.value.message, sessionStorage)
      employees.value = employees.value.filter((employee) => employee.employee_id != employeeId)
    }
  }
}

onMounted(() => {
  successMessage.value = getSessionStorage('employee-success')
})

onBeforeUnmount(() => {
  clearSuccessMessage()
})
</script>

<template>
  <section class="px-2">
    <SuccessAlert v-if="successMessage" class="w-1/3" @dismiss="clearSuccessMessage">{{ successMessage }}</SuccessAlert>
    <ErrorAlert v-if="errorMessage" class="w-1/3" @dismiss="() => errorMessage = null">
      {{errorMessage}}
    </ErrorAlert>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg py-7">
      <div class="flex items-center justify-between">
        <SearchForm @clearSearchQueries="clearSearchQueries">
          <SearchWithLabel
            label="Employee ID: "
            v-model="searchQueryByID"
            :input="idSearchInput"
          />
          <SearchWithLabel
            class="ms-3"
            label="Employee Name: "
            v-model="searchQueryByName"
            :input="nameSearchInput"
          />
        </SearchForm>
        <NuxtLink
          to="/employees/create"
          type="button"
          class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4
            focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 mb-2
            dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800"
        >
          New Employee
        </NuxtLink>
      </div>
      <Table>
        <template #table-header>
          <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
          <tr>
            <th scope="col" class="px-6 py-3">
              <div class="flex items-center">
                Employee ID
                <FilterArrows @click="changeSortingOrderByField('employee_id')"/>
              </div>
            </th>
            <th scope="col" class="px-6 py-3">
              <div class="flex items-center">
                Employee Name
                <FilterArrows @click="changeSortingOrderByField('employee_name')"/>
              </div>
            </th>
            <th scope="col" class="px-6 py-3">
              Email
            </th>
            <th scope="col" class="px-6 py-3">
              Profile Photo
            </th>
            <th scope="col" class="px-6 py-3">
              Address
            </th>
            <th scope="col" class="px-6 py-3">
              Phone
            </th>
            <th scope="col" class="px-6 py-3">
              <div class="flex items-center">
                Date of Birth
                <FilterArrows @click="changeSortingOrderByField('dob')"/>
              </div>
            </th>
            <th scope="col" class="px-6 py-3">
              Action
            </th>
          </tr>
          </thead>
        </template>
        <template #table-body>
          <tbody>
          <tr
            v-if="paginatedEmployees.length > 0"
            v-for="employee in paginatedEmployees"
            :key="employee.employee_id"
            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50"
          >
            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
              {{ employee.employee_id }}
            </th>
            <td class="px-6 py-4">
              {{ employee.employee_name }}
            </td>
            <td class="px-6 py-4">
              {{ employee.email }}
            </td>
            <td class="px-6 py-4">
              <img
                class="w-10 h-10 rounded-full"
                :src="storagePath + employee.profile"
                :alt="employee.employee_name"
              >
            </td>
            <td class="px-6 py-4">
              {{ employee.address }}
            </td>
            <td class="px-6 py-4">
              {{ employee.phone }}
            </td>
            <td class="px-6 py-4">
              {{ employee.dob }}
            </td>
            <td class="px-6 py-4">
              <NuxtLink
                :to="`/employees/${employee.employee_id}`"
                class="font-medium text-green-600 dark:text-green-500 hover:underline"
              >
                Show
              </NuxtLink>
              <NuxtLink
                :to="`/employees/${employee.employee_id}/edit`"
                class="font-medium text-blue-600 dark:text-blue-500 hover:underline mx-3"
              >
                Edit
              </NuxtLink>
              <button
                @click="deleteEmployee(employee.employee_id)"
                class="font-medium text-red-600 dark:text-red-500 hover:underline"
              >
                Delete
              </button>
            </td>
          </tr>
          <div v-else class="ps-5 py-5 w-full text-red-500 whitespace-nowrap">
            There is no employee
          </div>
          </tbody>
        </template>
      </Table>
      <UPagination
        v-if="employees.length > 5"
        class="mt-3 ms-4"
        v-model="page"
        :page-count="employeesPerPage"
        :total="employees.length"
      />
    </div>
  </section>
</template>