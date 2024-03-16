<script setup lang="ts">
import {Employee, Statistics} from "~/types/employee";
import {Task} from "~/types/task";
import {Project} from "~/types/project";
import {
  ADMIN,
  TASK_STATUS_CLOSE,
  TASK_STATUS_FINISH,
  TASK_STATUS_IN_PROGRESS,
  TASK_STATUS_OPEN
} from "~/constants/constants";

const inputBoxClass = 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 ' +
    'focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 ' +
    'dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500'

const {$api} = useNuxtApp()
const {formatDateTime} = useUtils()
const user: Employee = useCookie('user').value as unknown as Employee

const errorMessage = ref<string | null>(null)
const statistics = ref<Statistics>({});
const projects = ref<Project[]>([])
const employees = ref<Employee[]>([])
const notClosedTasks = ref<Task[]>([])
const page = ref(1)
const tasksPerPage = 5
const isLoading = ref(false)

const paginatedNotClosedTasks = computed(() => {
  const startIndex = (page.value - 1) * tasksPerPage
  const endIndex = startIndex + tasksPerPage
  return notClosedTasks.value.slice(startIndex, endIndex)
})

const getDashboardStatistics = async () => {
  const {data, error} = await $api.dashboard.getStatistics()
  if (error.value) errorMessage.value = error.value?.data.error
  if (data.value) statistics.value = data.value.statistics
}

const getEmployees = async () => {
  if (parseInt(user.position as string) == ADMIN) {
    const {data, error} = await $api.employee.getEmployees()
    if (error.value) errorMessage.value = error.value?.data.error
    if (data.value) employees.value = data.value.employees
  }
}

const getProjects = async () => {
  if (parseInt(user.position as string) == ADMIN) {
    const {data, error} = await $api.project.get()
    if (error.value) errorMessage.value = error.value?.data.error
    if (data.value) projects.value = data.value.projects
  }
}

const getNotClosedTasks = async () => {
  const {data, error} = await $api.task.get('unclosed')
  if (error.value) errorMessage.value = error.value?.data.error
  if (data.value) notClosedTasks.value = data.value.tasks
}

const fetchAllData = async () => {
  try {
    await Promise.all([
      getDashboardStatistics(),
      getEmployees(),
      getProjects(),
      getNotClosedTasks()
    ])
  } catch (error) {
    errorMessage.value = "An expected error occurred in getting data"
  }
}
await fetchAllData()

const updateTask = async (index: number) => {
  isLoading.value = true
  const task = paginatedNotClosedTasks.value[index]
  const updateTaskData = {
    title: task.title,
    description: task.description,
    project_id: task.project_id,
    assigned_member_id: task.assigned_member_id,
    estimate_hr: task.estimate_hr,
    actual_hr: task.actual_hr,
    status: task.status,
    estimate_start_date: task.estimate_start_date ? formatDateTime(new Date(task.estimate_start_date)) : null,
    estimate_finish_date: task.estimate_finish_date ? formatDateTime(new Date(task.estimate_finish_date)) : null,
    actual_start_date: task.actual_start_date ? formatDateTime(new Date(task.actual_start_date)) : null,
    actual_finish_date: task.actual_finish_date ? formatDateTime(new Date(task.actual_finish_date)) : null
  }

  const {data, error} = await $api.task.update((task.task_id as number)?.toString(), updateTaskData)
  if (error.value) errorMessage.value = error.value?.data.error
  if (data.value) removeClosedTasks()
  isLoading.value = false
}

const removeClosedTasks = () => {
  notClosedTasks.value = notClosedTasks.value.filter((task) => task.status !== TASK_STATUS_CLOSE)
}
</script>

<template>
  <ErrorAlert v-if="errorMessage" class="w-1/3 my-5" @dismiss="() => errorMessage = null">
    {{errorMessage}}
  </ErrorAlert>
  <div class="grid grid-cols-2 md:grid-cols-5 gap-3 px-12">
    <NuxtLink v-if="statistics.employees" to="/employees/list" class="p-6 rounded-lg border border-blue-800 dark:border-blue-700">
      <ul>
        <li>
          <svg class="w-full h-14 text-blue-800 dark:text-blue-700" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 19">
            <path d="M14.5 0A3.987 3.987 0 0 0 11 2.1a4.977 4.977 0 0 1 3.9 5.858A3.989 3.989 0 0 0 14.5 0ZM9 13h2a4 4 0 0 1 4 4v2H5v-2a4 4 0 0 1 4-4Z"/>
            <path d="M5 19h10v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2ZM5 7a5.008 5.008 0 0 1 4-4.9 3.988 3.988 0 1 0-3.9 5.859A4.974 4.974 0 0 1 5 7Zm5 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm5-1h-.424a5.016 5.016 0 0 1-1.942 2.232A6.007 6.007 0 0 1 17 17h2a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5ZM5.424 9H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h2a6.007 6.007 0 0 1 4.366-5.768A5.016 5.016 0 0 1 5.424 9Z"/>
          </svg>
        </li>
        <li class="text-center mt-3 text-blue-800 dark:text-blue-700">
          Employee
        </li>
        <li class="text-center text-7xl mt-3 text-blue-800 dark:text-blue-700">
          {{ statistics.employees }}
        </li>
      </ul>
    </NuxtLink>
    <NuxtLink v-if="statistics.projects" to="/projects/list" class="p-6 rounded-lg border border-yellow-400 dark:border-yellow-300">
      <ul>
        <li>
          <svg class="w-full h-14 text-yellow-400 dark:text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 16">
            <path d="M19 0H1a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1ZM2 6v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6H2Zm11 3a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V8a1 1 0 0 1 2 0h2a1 1 0 0 1 2 0v1Z"/>
          </svg>
        </li>
        <li class="text-center mt-3 text-yellow-400 dark:text-yellow-300">
          Projects
        </li>
        <li class="text-center text-7xl mt-3 text-yellow-400 dark:text-yellow-300">
          {{ statistics.projects }}
        </li>
      </ul>
    </NuxtLink>
    <NuxtLink v-if="notClosedTasks.length > 0" to="/tasks/list" class="p-6 rounded-lg border border-green-800 dark:border-green-500">
      <ul>
        <li>
          <svg class="w-full h-14 text-green-800 dark:text-green-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 21 18">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.5 3h9.563M9.5 9h9.563M9.5 15h9.563M1.5 13a2 2 0 1 1 3.321 1.5L1.5 17h5m-5-15 2-1v6m-2 0h4"/>
          </svg>
        </li>
        <li class="text-center mt-3 text-green-800 dark:text-green-500">
          Not Closed Tasks
        </li>
        <li class="text-center text-7xl mt-3 text-green-800 dark:text-green-500">
          {{ notClosedTasks.length }}
        </li>
      </ul>
    </NuxtLink>
    <NuxtLink v-if="statistics.reports" to="/reports/list" class="p-6 rounded-lg border border-blue-800 dark:border-blue-700">
      <ul>
        <li>
          <svg class="w-full h-14 text-blue-800 dark:text-blue-700" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
            <path d="M12.687 14.408a3.01 3.01 0 0 1-1.533.821l-3.566.713a3 3 0 0 1-3.53-3.53l.713-3.566a3.01 3.01 0 0 1 .821-1.533L10.905 2H2.167A2.169 2.169 0 0 0 0 4.167v11.666A2.169 2.169 0 0 0 2.167 18h11.666A2.169 2.169 0 0 0 16 15.833V11.1l-3.313 3.308Zm5.53-9.065.546-.546a2.518 2.518 0 0 0 0-3.56 2.576 2.576 0 0 0-3.559 0l-.547.547 3.56 3.56Z"/>
            <path d="M13.243 3.2 7.359 9.081a.5.5 0 0 0-.136.256L6.51 12.9a.5.5 0 0 0 .59.59l3.566-.713a.5.5 0 0 0 .255-.136L16.8 6.757 13.243 3.2Z"/>
          </svg>
        </li>
        <li class="text-center mt-3 text-blue-800 dark:text-blue-700">
          Reports
        </li>
        <li class="text-center text-7xl mt-3 text-blue-800 dark:text-blue-700">
          {{ statistics.reports }}
        </li>
      </ul>
    </NuxtLink>
    <NuxtLink :to="'/employees/' + user.id" class="p-6 rounded-lg border border-gray-700 dark:border-white">
      <ul>
        <li>
          <svg class="w-full h-14 text-gray-700 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 14 18">
            <path d="M7 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9Zm2 1H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z"/>
          </svg>
        </li>
        <li class="text-center mt-3 text-gray-700 dark:text-white">
          Profile
        </li>
      </ul>
    </NuxtLink>
  </div>
  <div class="mt-7">
    <h3 class="font-bold text-2xl text-center mb-5">Top Not Closed Tasks</h3>
    <Table class="relative overflow-x-auto shadow-md sm:rounded-lg">
      <template #table-header>
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
          <tr>
            <th scope="col" class="px-6 py-3">
              <span class="whitespace-nowrap">Task ID</span>
            </th>
            <th scope="col" class="px-6 py-3">
              <span class="whitespace-nowrap">Title</span>
            </th>
            <th scope="col" class="px-6 py-3">
              <span class="whitespace-nowrap">Description</span>
            </th>
            <th scope="col" class="px-6 py-3">
              <span class="whitespace-nowrap">Project Name</span>
            </th>
            <th scope="col" class="px-6 py-3">
              <span class="whitespace-nowrap">Assigned Employee</span>
            </th>
            <th scope="col" class="px-6 py-3">
              <span class="whitespace-nowrap">Estimate Hour</span>
            </th>
            <th scope="col" class="px-6 py-3">
              <span class="whitespace-nowrap">Actual Hour</span>
            </th>
            <th scope="col" class="px-6 py-3">
              <span class="whitespace-nowrap">Status</span>
            </th>
            <th scope="col" class="px-6 py-3">
              <span class="whitespace-nowrap">Estimate Start Date</span>
            </th>
            <th scope="col" class="px-6 py-3">
              <span class="whitespace-nowrap">Estimate Finish Date</span>
            </th>
            <th scope="col" class="px-6 py-3">
              <span class="whitespace-nowrap">Actual Start Date</span>
            </th>
            <th scope="col" class="px-6 py-3">
              <span class="whitespace-nowrap">Actual Finish Date</span>
            </th>
            <th scope="col" class="px-6 py-3">
              <span class="whitespace-nowrap">Action</span>
            </th>
          </tr>
        </thead>
      </template>
      <template #table-body>
        <tr
          v-if="paginatedNotClosedTasks.length > 0"
          v-for="(task, index) in paginatedNotClosedTasks"
          :key="task.task_id"
          class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50"
        >
          <td class="px-6 py-4 whitespace-nowrap">
            {{ task.task_id }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <input
              type="text"
              :class=inputBoxClass
              class="w-50"
              v-model="task.title"
            />
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <textarea
              :class="inputBoxClass"
              class="w-52"
              v-model="task.description"
              rows="3"
            ></textarea>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <select
              :class="inputBoxClass"
              class="w-60"
              @change="(e) => task.project_id = parseInt(e.target.value)"
            >
              <option
                v-for="project in projects"
                :key="project.project_id"
                :selected="task.project_id == project.project_id"
                :value="project.project_id"
              >
                {{ project.project_name }}
              </option>
            </select>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <select
              :class="inputBoxClass"
              class="w-60"
              @change="(e) => task.assigned_member_id = parseInt(e.target.value)"
            >
              <option
                v-for="employee in employees"
                :key="employee.employee_id"
                :selected="task.assigned_member_id == employee.employee_id"
                :value="employee.employee_id"
              >
                {{ employee.employee_name }}
              </option>
            </select>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <input
              type="number"
              :class="inputBoxClass"
              class="w-32"
              v-model="task.estimate_hr"
            />
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <input
              type="number"
              :class="inputBoxClass"
              class="w-32"
              v-model="task.actual_hr"
            />
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <select
              :class="inputBoxClass"
              class="w-40"
              @change="(e) => task.status = parseInt(e.target.value)"
            >
              <option
                :selected="task.status == TASK_STATUS_OPEN"
                value="0"
              >
                Open
              </option>
              <option
                :selected="task.status == TASK_STATUS_IN_PROGRESS"
                value="1"
              >
                In progress
              </option>
              <option
                :selected="task.status == TASK_STATUS_FINISH"
                value="2"
              >
                Finished
              </option>
              <option
                :selected="task.status == TASK_STATUS_CLOSE"
                value="3"
              >
                Closed
              </option>
            </select>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <DatePicker
              :enable-time-picker="true"
              v-model="task.estimate_start_date"
            />
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <DatePicker
              :enable-time-picker="true"
              v-model="task.estimate_finish_date"
            />
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <DatePicker
              :enable-time-picker="true"
              v-model="task.actual_start_date"
            />
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <DatePicker
              :enable-time-picker="true"
              v-model="task.actual_finish_date"
            />
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <button
              type="button"
              class="px-3 py-2 text-xs font-medium text-center inline-flex items-center text-white bg-blue-700 rounded-lg
                hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700
                dark:focus:ring-blue-800"
              @click="updateTask(index)"
              :disabled="isLoading"
            >
              <svg class="w-3 h-3 me-2 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                <path d="M12.687 14.408a3.01 3.01 0 0 1-1.533.821l-3.566.713a3 3 0 0 1-3.53-3.53l.713-3.566a3.01 3.01 0 0 1 .821-1.533L10.905 2H2.167A2.169 2.169 0 0 0 0 4.167v11.666A2.169 2.169 0 0 0 2.167 18h11.666A2.169 2.169 0 0 0 16 15.833V11.1l-3.313 3.308Zm5.53-9.065.546-.546a2.518 2.518 0 0 0 0-3.56 2.576 2.576 0 0 0-3.559 0l-.547.547 3.56 3.56Z"/>
                <path d="M13.243 3.2 7.359 9.081a.5.5 0 0 0-.136.256L6.51 12.9a.5.5 0 0 0 .59.59l3.566-.713a.5.5 0 0 0 .255-.136L16.8 6.757 13.243 3.2Z"/>
              </svg>
              Update
            </button>
          </td>
        </tr>
      </template>
    </Table>
    <UPagination
      v-if="notClosedTasks.length > 5"
      class="mt-3 ms-4"
      v-model="page"
      :page-count="tasksPerPage"
      :total="notClosedTasks.length"
    />
  </div>
</template>