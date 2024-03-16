<script setup lang="ts">
import {Task} from "~/types/task";
import {
  TASK_STATUS_CLOSE,
  TASK_STATUS_FINISH,
  TASK_STATUS_IN_PROGRESS,
  TASK_STATUS_OPEN
} from "~/constants/constants";

const {$api} = useNuxtApp()
const {getSessionStorage, clearSessionStorage, compareNumbers, compareStrings, compareDates} = useUtils()

type StatusKey = 'all' | 'open' | 'closed' | 'not-closed' | 'finished' | 'in-progress'

const inputClass = 'block p-2 text-sm text-gray-900 border border-gray-300 rounded-lg w-60 bg-gray-50 focus:ring-blue-500 ' +
    'focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white ' +
    'dark:focus:ring-blue-500 dark:focus:border-blue-500'

const inputs = {
  title: {
    name: 'title_search',
    type: 'text',
    class: inputClass
  },
  project: {
    name: 'project_search',
    type: 'text',
    class: inputClass
  },
  employee: {
    name: 'employee_search',
    type: 'text',
    class: inputClass
  }
}

const downloadBtn = {
  type: 'button',
  class: 'focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium ' +
      'rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800'
}

const statusFilterBtn = {
  type: 'button',
  class: 'px-4 py-2 text-sm font-medium text-gray-900 bg-transparent border border-gray-900 hover:bg-gray-900 ' +
      'hover:text-white dark:border-white dark:text-white dark:hover:text-white dark:hover:bg-gray-700'
}

const successMessage = ref<string | null>(null)
const errorMessage = ref<string | null>(null)
const searchQueryByTitle = ref('')
const searchQueryByProject = ref('')
const searchQueryByEmployee = ref('')
const selectedStatus = ref('all')
const sortingOrder = ref({field: 'task_id', direction: 'asc'})
const tasks = ref<Task[]>([])
const page = ref(1)
const tasksPerPage = 5

const showTasks = computed(() => {
  const startIndex = (page.value - 1) * tasksPerPage
  const endIndex = startIndex + tasksPerPage

  const filteredAndSortedTasks = getFilteredAndSortedTasks()
  return filteredAndSortedTasks.slice(startIndex, endIndex)
})

const getFilteredAndSortedTasks = () => {
  const filteredTasks = tasks.value.filter(filterBy)
  const sortFunction = getSortFunction()
  return filteredTasks.sort(sortFunction)
}

const filterBy = (task: Task) => {
  const matchedTaskTitle = !searchQueryByTitle.value
      || (task.title as string).toLowerCase().includes(searchQueryByTitle.value.toLowerCase());
  const matchedProjectName = !searchQueryByProject.value
      || (task.project?.project_name as string).toLowerCase().includes(searchQueryByProject.value.toLowerCase())
  const matchedEmployeeName = !searchQueryByEmployee.value
      || (task.member?.employee_name as string).toLowerCase().includes(searchQueryByEmployee.value.toLowerCase())
  const matchedStatus = !selectedStatus.value || filteredByStatus(selectedStatus.value, task)

  return matchedTaskTitle && matchedProjectName && matchedEmployeeName && matchedStatus;
}

const filteredByStatus = (status: string, task: Task) => {
  const statusAndCompare: { [key in StatusKey]: boolean } = {
    'all': true,
    'open': task.status === TASK_STATUS_OPEN,
    'closed': task.status === TASK_STATUS_CLOSE,
    'not-closed': task.status !== TASK_STATUS_CLOSE,
    'finished': task.status === TASK_STATUS_FINISH,
    'in-progress': task.status === TASK_STATUS_IN_PROGRESS
  }

  return statusAndCompare[status as StatusKey]
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
  'task_id': compareNumbers,
  'title': compareStrings,
  'project_id': compareNumbers,
  'assigned_member_id': compareNumbers,
  'estimate_hr': compareDates,
  'status': compareNumbers,
  'estimate_start_date': compareDates,
  'estimate_finish_date': compareDates,
  'actual_start_date': compareDates,
  'actual_finish_date': compareDates,
}

const changeSortingOrderByField = (field: string) => {
  const direction = sortingOrder.value.direction === 'asc' ? 'desc' : 'asc'
  sortingOrder.value.field = field
  sortingOrder.value.direction = direction
}

const {data, error} = await $api.task.get()
if (data.value) tasks.value = data.value.tasks
if (error.value) errorMessage.value = error.value.data.error

const clearSearchQueries = () => {
  searchQueryByTitle.value = ''
  searchQueryByProject.value = ''
  searchQueryByEmployee.value = ''
  selectedStatus.value = 'all'
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
      <div class="inline-flex items-center rounded-md shadow-sm ms-5 mb-7" role="group">
        <div class="me-8">Status</div>
        <Button
          :button="statusFilterBtn"
          class="rounded-s-lg"
          :class="[selectedStatus === 'open' ? 'ring-2 ring-gray-500 bg-gray-900 text-white dark:bg-gray-700' : '']"
          @click="() => selectedStatus = 'open'"
        >
          Open
        </Button>
        <Button
          :button="statusFilterBtn"
          :class="[selectedStatus === 'in-progress' ? 'ring-2 ring-gray-500 bg-gray-900 text-white dark:bg-gray-700' : '']"
          @click="() => selectedStatus = 'in-progress'"
        >
          In progress
        </Button>
        <Button
          :button="statusFilterBtn"
          :class="[selectedStatus === 'finished' ? 'ring-2 ring-gray-500 bg-gray-900 text-white dark:bg-gray-700' : '']"
          @click="() => selectedStatus = 'finished'"
        >
          Finished
        </Button>
        <Button
          :button="statusFilterBtn"
          :class="[selectedStatus === 'closed' ? 'ring-2 ring-gray-500 bg-gray-900 text-white dark:bg-gray-700' : '']"
          @click="() => selectedStatus = 'closed'"
        >
          Closed
        </Button>
        <Button
          :button="statusFilterBtn"
          :class="[selectedStatus === 'not-closed' ? 'ring-2 ring-gray-500 bg-gray-900 text-white dark:bg-gray-700' : '']"
          @click="() => selectedStatus = 'not-closed'"
        >
          Not Closed
        </Button>
        <Button
          :button="statusFilterBtn"
          class="rounded-e-lg"
          :class="[selectedStatus === 'all' ? 'ring-2 ring-gray-500 bg-gray-900 text-white dark:bg-gray-700' : '']"
          @click="() => selectedStatus = 'all'"
        >
          All
        </Button>
      </div>
      <div>
        <SearchForm @clearSearchQueries="clearSearchQueries">
          <SearchWithLabel
            label="Title: "
            v-model="searchQueryByTitle"
            :input="inputs.title"
          />
          <SearchWithLabel
              label="Project: "
              v-model="searchQueryByProject"
              :input="inputs.project"
              class="ms-5"
          />
          <SearchWithLabel
              label="Assigned Employee: "
              v-model="searchQueryByEmployee"
              :input="inputs.employee"
              class="mx-5"
          />
        </SearchForm>
      </div>
      <div class="ms-5 mb-3">
        <Button
          :button="downloadBtn"
          :disabled="false"
          @click="() => $api.task.export()"
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
          to="/tasks/create"
          type="button"
          class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4
            focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center ms-3 mb-2
            dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800"
        >
          New Task
        </NuxtLink>
      </div>
      <Table class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <template #table-header>
          <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
              <th scope="col" class="px-6 py-3">
                <div class="flex items-center">
                  <span class="whitespace-nowrap">Task ID</span>
                  <FilterArrows @click="changeSortingOrderByField('task_id')"/>
                </div>
              </th>
              <th scope="col" class="px-6 py-3">
                <div class="flex items-center">
                  Title
                  <FilterArrows @click="changeSortingOrderByField('title')"/>
                </div>
              </th>
              <th scope="col" class="px-6 py-3">
                Description
              </th>
              <th scope="col" class="px-6 py-3">
                <div class="flex items-center">
                  <span class="whitespace-nowrap">Project Name</span>
                  <FilterArrows @click="changeSortingOrderByField('project_id')"/>
                </div>
              </th>
              <th scope="col" class="px-6 py-3">
                <div class="flex items-center">
                  <span class="whitespace-nowrap">Assigned Employee</span>
                  <FilterArrows @click="changeSortingOrderByField('assigned_member_id')"/>
                </div>
              </th>
              <th scope="col" class="px-6 py-3">
                <div class="flex items-center">
                  <span class="whitespace-nowrap">Estimate Hour</span>
                  <FilterArrows @click="changeSortingOrderByField('estimate_hr')"/>
                </div>
              </th>
              <th scope="col" class="px-6 py-3">
                <div class="flex items-center">
                  <span class="whitespace-nowrap">Actual Hour</span>
                  <FilterArrows @click="changeSortingOrderByField('actual_hr')"/>
                </div>
              </th>
              <th scope="col" class="px-6 py-3">
                <div class="flex items-center">
                  Status
                  <FilterArrows @click="changeSortingOrderByField('status')"/>
                </div>
              </th>
              <th scope="col" class="px-6 py-3">
                <div class="flex items-center">
                  <span class="whitespace-nowrap">Estimate Start Date</span>
                  <FilterArrows @click="changeSortingOrderByField('estimate_start_date')"/>
                </div>
              </th>
              <th scope="col" class="px-6 py-3">
                <div class="flex items-center">
                  <span class="whitespace-nowrap">Estimate Finish Date</span>
                  <FilterArrows @click="changeSortingOrderByField('estimate_finish_date')"/>
                </div>
              </th>
              <th scope="col" class="px-6 py-3">
                <div class="flex items-center">
                  <span class="whitespace-nowrap">Actual Start Date</span>
                  <FilterArrows @click="changeSortingOrderByField('actual_start_date')"/>
                </div>
              </th>
              <th scope="col" class="px-6 py-3">
                <div class="flex items-center">
                  <span class="whitespace-nowrap">Actual Finish Date</span>
                  <FilterArrows @click="changeSortingOrderByField('actual_finish_date')"/>
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
              v-if="showTasks.length > 0"
              v-for="task in showTasks"
              :key="task.task_id"
              class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50"
            >
              <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                {{ task.task_id }}
              </th>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ task.title }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ task.description }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ task.project?.project_name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ task.member?.employee_name }}
              </td>
              <td class="px-6 py-4">
                {{ task.estimate_hr }}
              </td>
              <td class="px-6 py-4">
                {{ task.actual_hr }}
              </td>
              <td class="px-6 py-4">
                <span
                  v-if="task.status === TASK_STATUS_OPEN"
                  class="px-2 py-1 bg-blue-500 text-white"
                >
                  Open
                </span>
                <span
                  v-if="task.status === TASK_STATUS_IN_PROGRESS"
                  class="px-2 py-1 bg-yellow-500 text-white whitespace-nowrap"
                >
                  In Progress
                </span>
                <span
                  v-if="task.status === TASK_STATUS_FINISH"
                  class="px-2 py-1 bg-green-500 text-white"
                >
                  Finished
                </span>
                <span
                  v-if="task.status === TASK_STATUS_CLOSE"
                  class="px-2 py-1 bg-gray-500 text-white"
                >
                  Closed
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ task.estimate_start_date }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ task.estimate_finish_date }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ task.actual_start_date }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ task.actual_finish_date }}
              </td>
              <td class="px-6 py-4">
                <NuxtLink
                  :to="`/tasks/${task.task_id}/edit`"
                  class="font-medium text-blue-600 dark:text-blue-500 hover:underline mx-3">
                  Edit
                </NuxtLink>
              </td>
            </tr>
            <div v-else class="ps-5 py-5 w-full text-red-500 whitespace-nowrap">
              There is no task
            </div>
          </tbody>
        </template>
      </Table>
      <UPagination
        v-if="tasks.length > 5"
        class="mt-3 ms-4"
        v-model="page"
        :page-count="tasksPerPage"
        :total="tasks.length"
      />
    </div>
  </section>
</template>