<script setup lang="ts">
import {Employee} from '~/types/employee';

const emit = defineEmits()
const {$api} = useNuxtApp()
const config = useRuntimeConfig()

const employeesPerPage = 3
const storagePath = config.public.storagePath

const closeModelBtn = {
  type: 'button',
  class: 'absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg ' +
      'text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white',
  modelId: 'target-person-selection-modal'
}

const searchQueriesClearBtn = {
  type: 'button',
  class: 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 ' +
      'py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800'
}

const clearSelectionBtn = {
  type: 'button',
  class: 'text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 ' +
      'focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-gray-800 dark:text-white ' +
      'dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700'
}

const decisionBtn = {
  type: 'button',
  class: 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 ' +
      'py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800',
  modelId: 'target-person-selection-modal'
}

const inputClass = 'block p-2 text-sm text-gray-900 border border-gray-300 rounded-lg w-30 bg-gray-50 ' +
    'focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 ' +
    'dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500'

const searchInputs = {
  project: {
    name: 'project_search',
    type: 'text',
    class: inputClass
  },
  name: {
    name: 'name_search',
    type: 'text',
    class: inputClass
  },
  email: {
    name: 'email_search',
    type: 'text',
    class: inputClass
  }
}

const {oldSelectedEmployees} = defineProps({
  oldSelectedEmployees: {
    type: Array as () => Employee[],
    default: []
  }
})

const page = ref(1)
const employees = ref([])
const isAllChecked = ref(false)
const errorMessage = ref<string|null>(null)
const searchQueries = reactive({
  project: '',
  name: '',
  email: ''
})

const {data, error} = await $api.employee.getEmployees()
if (data.value) {
  data.value.employees.forEach((employee: Employee) => {
    employee.checked = oldSelectedEmployees?.some(selectedEmployee =>
        selectedEmployee.employee_id === employee.employee_id);
  });
  employees.value = data.value.employees
}

if (error.value) {
  errorMessage.value = error.value.data.error
}

const handleCheckChange = () => {
  if (isAllChecked.value) {
    employees.value.forEach((employee: Employee) => {
      employee.checked = true
    });
  } else {
    employees.value.forEach((employee: Employee) => {
      employee.checked = false
    });
  }
}

const clearCheckedStates = () => {
  isAllChecked.value = false
  employees.value.forEach((employee: Employee) => {
    employee.checked = false
  })
}

const selectedEmployees = computed(() => employees.value.filter(((employee: Employee) => employee.checked === true)))

const paginatedEmployees = computed(() => {
  const startIndex = (page.value - 1) * employeesPerPage
  const endIndex = startIndex + employeesPerPage
  const filteredEmployees = employees.value.filter(filterByProjectNameEmail)

  return filteredEmployees.slice(startIndex, endIndex)
})

const filterByProjectNameEmail = (employee: Employee) => {
  const matchProject = !searchQueries.project || employee?.project?.project_name.toLowerCase().includes(searchQueries.project.toLowerCase());
  const matchName = !searchQueries.name || employee?.employee_name?.toLowerCase().includes(searchQueries.name.toLowerCase());
  const matchEmail = !searchQueries.email || employee.email.toLowerCase().includes(searchQueries.email.toLowerCase());

  return matchProject && matchName && matchEmail;
}

const handleDecision = () => emit('selected-employees', selectedEmployees.value)

const clearSearchQueries = () => {
  searchQueries.project = ''
  searchQueries.name = ''
  searchQueries.email = ''
}
</script>

<template>
  <div id="target-person-selection-modal" tabindex="-1" aria-hidden="true"
       class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center
         w-full md:inset-0 h-modal md:h-full"
  >
    <div class="relative p-4 w-full max-w-5xl h-full md:h-auto">
      <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
        <Button
          :button="closeModelBtn"
        >
          <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
          </svg>
        </Button>
        <div class="px-6 py-6 lg:px-8">
          <h3 class="mb-6 text-xl font-medium text-gray-900 dark:text-white text-center">
            Target person selection
          </h3>
          <ErrorAlert v-if="errorMessage" class="w-1/3" @dismiss="() => errorMessage = null">{{ errorMessage }}</ErrorAlert>
          <div class="flex items-center my-6">
            <SearchWithLabel
                class="mr-3"
                label="Project: "
                v-model="searchQueries.project"
                :input="searchInputs.project"
            />
            <SearchWithLabel
                class="mr-3"
                label="Name: "
                v-model="searchQueries.name"
                :input="searchInputs.name"
            />
            <SearchWithLabel
                class="mr-3"
                label="Email: "
                v-model="searchQueries.email"
                :input="searchInputs.email"
            />
            <div class="ml-4">
              <Button
                :button="searchQueriesClearBtn"
                @click="clearSearchQueries"
              >
                Clear
              </Button>
            </div>
          </div>
          <div class="my-4">Number of target members: {{ employees.length }}</div>
          <div class="relative overflow-x-auto">
            <Table>
              <template #table-header>
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                  <th scope="col" class="p-4">
                    <div class="flex items-center">
                      <input id="checkbox-all-search" type="checkbox"
                             class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500
                          dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800
                          focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                             @change="handleCheckChange"
                             v-model="isAllChecked"
                      />
                    </div>
                  </th>
                  <th scope="col" class="px-6 py-3"></th>
                  <th scope="col" class="px-6 py-3">
                    Name
                  </th>
                  <th scope="col" class="px-6 py-3">
                    Email
                  </th>
                  <th scope="col" class="px-6 py-3">
                    Date of Birth
                  </th>
                  <th scope="col" class="px-6 py-3"></th>
                </tr>
                </thead>
              </template>
              <template #table-body>
                <tbody>
                <tr
                  v-if="paginatedEmployees.length > 0"
                  class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                  v-for="employee in paginatedEmployees"
                  :key="employee.employee_id"
                >
                  <td class="w-4 p-4">
                    <div class="flex items-center">
                      <input
                          id="checkbox-table-search-1"
                          type="checkbox"
                          class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500
                          dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800
                          focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                          v-model="employee.checked"
                      />
                    </div>
                  </td>
                  <td scope="row" class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                    <img
                        class="w-10 h-10 rounded-full"
                        :src="storagePath + employee.profile"
                        :alt="employee.employee_name"
                    />
                  </td>
                  <td class="px-6 py-4">
                    {{ employee.employee_name }}
                  </td>
                  <td class="px-6 py-4">
                    {{ employee.email }}
                  </td>
                  <td class="px-6 py-4">
                    {{ employee.dob }}
                  </td>
                  <td class="px-6 py-4">
                    <NuxtLink to="#" class="text-blue-600 dark:text-blue-500 hover:underline">details</NuxtLink>
                  </td>
                </tr>
                <tr v-else class="text-red-700">
                  There is no employee.
                </tr>
                </tbody>
              </template>
            </Table>
            <UPagination class="mt-3" v-model="page" :page-count="employeesPerPage" :total="employees.length"/>
          </div>
          <div class="my-4">Number of person selected: {{ selectedEmployees.length }}</div>
          <div class="flex justify-center">
            <Button
              :button="clearSelectionBtn"
              @click="clearCheckedStates"
            >
              Clear Selection
            </Button>
            <Button
              :button="decisionBtn"
              @click="handleDecision"
            >
              Decision
            </Button>
<!--            <button-->
<!--                type="button"-->
<!--                class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100-->
<!--                focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-gray-800 -->
<!--                dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"-->
<!--                @click="clearCheckedStates"-->
<!--            >-->
<!--              Clear Selection-->
<!--            </button>-->
<!--            <button-->
<!--                type="button"-->
<!--                @click="handleDecision"-->
<!--                data-modal-hide="target-person-selection-modal"-->
<!--                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium-->
<!--                rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none -->
<!--                dark:focus:ring-blue-800"-->
<!--            >-->
<!--              Decision-->
<!--            </button>-->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>