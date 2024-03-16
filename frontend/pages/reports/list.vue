<script setup lang="ts">
import {Report} from "~/types/report";

const {$api} = useNuxtApp()
const {getSessionStorage, clearSessionStorage, compareNumbers, compareStrings, compareDates} = useUtils()

const inputClass = 'block p-2 text-sm text-gray-900 border border-gray-300 rounded-lg w-60 bg-gray-50 focus:ring-blue-500 ' +
    'focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white ' +
    'dark:focus:ring-blue-500 dark:focus:border-blue-500'

const inputs = {
  date: {
    name: 'date_search',
    type: 'date',
    class: inputClass
  },
  reportedBy: {
    name: 'reported_by_search',
    type: 'text',
    class: inputClass
  },
  reportTo: {
    name: 'report_to_search',
    type: 'text',
    class: inputClass
  }
}

const downloadBtn = {
  type: 'button',
  class: 'focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium ' +
      'rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800'
}

const successMessage = ref<string | null>(null)
const errorMessage = ref<string | null>(null)
const searchQueryByDate = ref('')
const searchQueryByReportedBy = ref('')
const searchQueryByReportTo = ref('')
const sortingOrder = ref({field: 'report_id', direction: 'asc'})
const reports = ref<Report[]>([])
const page = ref(1)
const reportsPerPage = 5

const showReports = computed(() => {
  const startIndex = (page.value - 1) * reportsPerPage
  const endIndex = startIndex + reportsPerPage

  const filteredAndSortedReports = getFilteredAndSortedReports()
  return filteredAndSortedReports.slice(startIndex, endIndex)
})

const getFilteredAndSortedReports = () => {
  const filteredTasks = reports.value.filter(filterBy)
  const sortFunction = getSortFunction()
  return filteredTasks.sort(sortFunction)
}

const filterBy = (report: Report) => {
  const matchedDate = !searchQueryByDate.value
      || (report.date as string).toLowerCase().includes(searchQueryByDate.value.toLowerCase());
  const matchedReportedBy = !searchQueryByReportedBy.value
      || (report.reporter?.employee_name as string).toLowerCase().includes(searchQueryByReportedBy.value.toLowerCase())
  const matchedReportTo = !searchQueryByReportTo.value
      || (report.admin?.employee_name as string).toLowerCase().includes(searchQueryByReportTo.value.toLowerCase())

  return matchedDate && matchedReportedBy && matchedReportTo;
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
  'report_id': compareNumbers,
  'date': compareDates,
}

const changeSortingOrderByField = (field: string) => {
  const direction = sortingOrder.value.direction === 'asc' ? 'desc' : 'asc'
  sortingOrder.value.field = field
  sortingOrder.value.direction = direction
}

const {data, error} = await $api.report.get()
if (data.value) reports.value = data.value.reports
if (error.value) errorMessage.value = error.value?.data.error

const clearSearchQueries = () => {
  searchQueryByDate.value = ''
  searchQueryByReportedBy.value = ''
  searchQueryByReportTo.value = ''
}

const clearSuccessMessage = () => {
  clearSessionStorage('task-success')
  successMessage.value = null
}

onMounted(() => {
  successMessage.value = getSessionStorage('task-success')
})

onBeforeUnmount(() => {
  clearSuccessMessage()
})
</script>

<template>
  <section class="px-2">
    <SuccessAlert v-if="successMessage" class="w-1/3" @dismiss="clearSuccessMessage">{{ successMessage }}</SuccessAlert>
    <ErrorAlert v-if="errorMessage" class="w-1/3" @dismiss="() => errorMessage = null">{{ errorMessage }}</ErrorAlert>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
      <div>
        <SearchForm @clearSearchQueries="clearSearchQueries">
          <SearchWithLabel
              label="Date: "
              v-model="searchQueryByDate"
              :input="inputs.date"
          />
          <SearchWithLabel
              label="Reported by: "
              v-model="searchQueryByReportedBy"
              :input="inputs.reportedBy"
              class="ms-5"
          />
          <SearchWithLabel
              label="Report to: "
              v-model="searchQueryByReportTo"
              :input="inputs.reportTo"
              class="mx-5"
          />
        </SearchForm>
      </div>
      <div class="ms-5 mb-3">
        <Button
            :button="downloadBtn"
            :disabled="false"
            @click="() => $api.report.export()"
        >
          <svg
            class="w-4 h-4 text-white inline-block"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 16 18"
          >
            <path
              stroke="currentColor"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M8 1v11m0 0 4-4m-4 4L4 8m11 4v3a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-3"
            />
          </svg>
          Download
        </Button>
        <NuxtLink
          to="/reports/create"
          type="button"
          class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4
          focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center ms-3 mb-2
          dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800"
        >
          New Report
        </NuxtLink>
      </div>
      <Table class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <template #table-header>
          <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
          <tr>
            <th scope="col" class="px-6 py-3">
              <div class="flex items-center">
                <span class="whitespace-nowrap">Report ID</span>
                <FilterArrows @click="changeSortingOrderByField('report_id')"/>
              </div>
            </th>
            <th scope="col" class="px-6 py-3">
              <div class="flex items-center">
                Date
                <FilterArrows @click="changeSortingOrderByField('date')"/>
              </div>
            </th>
            <th scope="col" class="px-6 py-3">
              Description
            </th>
            <th scope="col" class="px-6 py-3">
              <div class="flex items-center">
                <span class="whitespace-nowrap">Report To</span>
              </div>
            </th>
            <th scope="col" class="px-6 py-3">
              <div class="flex items-center">
                <span class="whitespace-nowrap">Reported By</span>
              </div>
            </th>
          </tr>
          </thead>
        </template>
        <template #table-body>
          <tbody>
          <tr
              v-if="showReports.length > 0"
              v-for="report in showReports"
              :key="report.report_id"
              class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50"
          >
            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
              {{ report.report_id }}
            </th>
            <td class="px-6 py-4 whitespace-nowrap">
              {{ report.date }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span v-html="report.description?.replace(/\n/g, '<br>')">
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              {{ report.admin?.employee_name }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              {{ report.reporter?.employee_name }}
            </td>
          </tr>
          <div v-else class="ps-5 py-5 w-full text-red-500 whitespace-nowrap">
            There is no report
          </div>
          </tbody>
        </template>
      </Table>
      <UPagination
        v-if="reports.length > 5"
        class="my-3 ms-4"
        v-model="page"
        :page-count="reportsPerPage"
        :total="reports.length"
      />
    </div>
  </section>
</template>