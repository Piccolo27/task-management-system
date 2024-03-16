<script setup lang="ts">
import {Employee} from "~/types/employee";
import {Task} from "~/types/task";
import {Report, ReportValidationError} from "~/types/report";
import {Project} from "~/types/project";
import {format} from "date-fns";
import {initModals} from "flowbite";
import {useStorage} from "@vueuse/core";

const {$api} = useNuxtApp()
const user: Employee = useCookie('user').value as unknown as Employee

const inputClass = 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 ' +
    'focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 ' +
    'dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500'

const inputs = reactive({
  admin: {
    type: 'select',
    name: 'admin',
    id: 'admin',
    class: 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 ' +
        'block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white ' +
        'dark:focus:ring-blue-500 dark:focus:border-blue-500',
    label: {
      for: 'admin',
      text: 'Admin',
      isRequired: true
    },
    selectOptions: []
  },
  problemOrFeeling: {
    type: 'textarea',
    name: 'problem_or_feeling',
    id: 'problem_or_feeling',
    class: 'block p-2.5 mt-3 w-80 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 ' +
        'focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white ' +
        'dark:focus:ring-blue-500 dark:focus:border-blue-500',
    rows: 5,
    label: {
      for: 'problem_or_feeling',
      text: 'Problem/Feeling',
      isRequired: false
    }
  }
})

const isLoading = ref(false)
const errorMessage = ref<string | null>(null)
const tasks= ref<Task[]>()
const reports = ref<Report[]>([])
const reportTo = ref<string | null>(null)
const problemOrFeeling = ref('')

const singleReportTemplate = {
  report_to: null,
  task_id: null,
  task_title: null,
  project_name: null,
  percentage: null,
  task_type: null,
  task_status: null,
  hour: null
}

const singleReport = reactive({...singleReportTemplate})

const validationErrors = reactive<ReportValidationError>({
  report_to: null
})

const loadData = async (apiCall: Function, successCallback: Function) => {
  const { data, error } = await apiCall();
  if (data.value) {
    successCallback(data.value);
  }
  if (error.value) {
    errorMessage.value = error.value?.data.error || 'An unknown error occurred';
  }
}

loadData($api.employee.getAdmins, (adminData: {employees: Employee[]}) => {
  adminData.employees.forEach((admin: Employee) => {
    const data = { [admin.employee_id?.toString() as string]: admin.employee_name }
    inputs.admin.selectOptions.push(data as never)
  })
})

loadData($api.task.get, (taskData: {tasks: Task[]}) => {
  tasks.value = taskData.tasks
})

const addReport = () => {
  reports.value.push({...singleReport})
  Object.assign(singleReport, singleReportTemplate)
}

const removeReport = (reportIndex: number) => {
  if (reportIndex > -1 && reportIndex < reports.value.length) {
    reports.value.splice(reportIndex, 1);
  }
}

const handleTaskIdChange = (taskId: number, reportIndex: number) => {
  if (taskId != null) {
    const selectedTask = (tasks.value as Task[]).filter((task: Task) => task.task_id == taskId)[0]
    reports.value[reportIndex].task_title = selectedTask.title
    reports.value[reportIndex].project_name = (selectedTask.project as Project).project_name
  } else {
    reports.value[reportIndex].task_title = ''
    reports.value[reportIndex].project_name = ''
  }
}

const sendReport = async() => {
  if (reports.value.length <= 0) {
    errorMessage.value = "Fill the report first!"
    return
  } else {
    let projectReports = ''
    reports.value.forEach((report, index) => {
      projectReports += `(${index + 1})⇒ ${report.task_title + ' ' +
        (report.percentage ? `<${report.percentage}%完了>` : '') +
        (report.hour ? `(${report.hour}hr)` : '')}`

      if (reports.value.length > 1) projectReports += '\n'
    })

    const reportDescription = `
      Name: ${user.name}
      Project: ${reports.value[0].project_name}
      【実績】
      ${projectReports}
      【所感】
      1)⇒ ${problemOrFeeling.value}
    `
    const formData: Report = {
      report_to: reportTo.value,
      description: reportDescription
    }

    clearMessages()
    isLoading.value = true
    const {data, error} = await $api.report.create(formData)

    if (data.value) {
      useStorage('report-success', data.value.message, sessionStorage)
      isLoading.value = false
      return navigateTo('/reports/list')
    }

    if (error.value) {
      setErrorMessages(error.value)
      isLoading.value = false
    }
  }
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

onMounted(() => {
  initModals()
})

onBeforeUnmount(() => {
  clearMessages()
})
</script>

<template>
  <section class="px-2 pt-8">
    <ErrorAlert class="w-80" v-if="errorMessage" @dismiss="clearMessages">{{ errorMessage }}</ErrorAlert>
    <div class="w-72 mb-5">
      <InputGroup
        v-model="reportTo"
        :input="inputs.admin"
        :error-message="validationErrors.report_to"
      />
    </div>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
      <Table class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <template #table-header>
          <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
          <tr>
            <th scope="col" class="px-6 py-3">
              <span class="whitespace-nowrap">No</span>
            </th>
            <th scope="col" class="px-6 py-3">
              <span class="whitespace-nowrap">Task ID</span>
            </th>
            <th scope="col" class="px-6 py-3">
              <span class="whitespace-nowrap">Task Title</span>
            </th>
            <th scope="col" class="px-6 py-3">
              <span class="whitespace-nowrap">Project</span>
            </th>
            <th scope="col" class="px-6 py-3">
              <span class="whitespace-nowrap">Percentage</span>
            </th>
            <th scope="col" class="px-6 py-3">
              <span class="whitespace-nowrap">Type</span>
            </th>
            <th scope="col" class="px-6 py-3">
              <span class="whitespace-nowrap">Status</span>
            </th>
            <th scope="col" class="px-6 py-3">
              <span class="whitespace-nowrap">Hour</span>
            </th>
            <th scope="col"></th>
          </tr>
          </thead>
        </template>
        <template #table-body>
          <tbody>
          <tr
            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50"
            v-for="(report, index) in reports"
            :key="index"
          >
            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
              {{ index + 1 }}
            </th>
            <td class="px-6 py-4">
              <select
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500
                  focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400
                  dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                v-model="report.task_id"
                @change="(e) => handleTaskIdChange(e.target.value, index)"
              >
                <option :value="null">Select Task ID</option>
                <option v-for="task in tasks" :value="task.task_id">{{ task.task_id }}</option>
              </select>
            </td>
            <td class="px-6 py-4">
              <input
                type="text"
                :value="report.task_title"
                class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500
                  focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600
                  dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                disabled
                readonly
              >
            </td>
            <td class="px-6 py-4">
              <input
                type="text"
                :value="report.project_name"
                class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500
                  focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600
                  dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                disabled
                readonly
              >
            </td>
            <td class="px-6 py-4">
              <div class="flex">
                <input
                  v-model="report.percentage"
                  type="text"
                  class="rounded-none rounded-s-lg bg-gray-50 border text-gray-900
                    focus:ring-blue-500 focus:border-blue-500 block flex-1 min-w-0 w-full text-sm border-gray-300 p-2.5
                    dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500
                    dark:focus:border-blue-500"
                />
                <span
                  class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border rounded-e-0 border-gray-300
                    rounded-e-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600"
                >
                  %
                </span>
              </div>
            </td>
            <td class="px-6 py-4">
              <select
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500
                    focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400
                    dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  v-model="report.task_type"
              >
                <option :value="null">Select Type</option>
                <option value="Low">Low</option>
                <option value="Middle">Middle</option>
                <option value="High">High</option>
              </select>
            </td>
            <td class="px-6 py-4">
              <select
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500
                    focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400
                    dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  v-model="report.task_status"
              >
                <option :value="null">Select Status</option>
                <option value="Close">Close</option>
                <option value="In progress">In progress</option>
                <option value="Finished">Finished</option>
                <option value="Open">Open</option>
              </select>
            </td>
            <td class="px-6 py-4">
              <div class="flex">
                <input
                    v-model="report.hour"
                    type="text"
                    class="rounded-none rounded-s-lg bg-gray-50 border text-gray-900
                    focus:ring-blue-500 focus:border-blue-500 block flex-1 min-w-0 w-full text-sm border-gray-300 p-2.5
                    dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500
                    dark:focus:border-blue-500"
                />
                <span
                    class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border rounded-e-0 border-gray-300
                    rounded-e-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600"
                >
                  hr
                </span>
              </div>
            </td>
            <td class="px-6 py-4">
              <button @click="removeReport(index)">
                <svg
                  class="w-6 h-6 text-gray-800 dark:text-white"
                  aria-hidden="true"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/>
                </svg>
              </button>
            </td>
          </tr>
          </tbody>
        </template>
      </Table>
      <div class="flex justify-center pb-3">
        <button
          type="button"
          class="mt-3 px-3 py-2 text-sm font-medium text-center inline-flex items-center text-white bg-blue-700 rounded-lg
          hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700
          dark:focus:ring-blue-800"
          @click="addReport"
        >
          <svg
              class="w-3 h-3 text-gray-800 dark:text-white me-1.5"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 18 18"
          >
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16"/>
          </svg>
          Add
        </button>
      </div>
      <div>
        <InputGroup
          v-model="problemOrFeeling"
          :input="inputs.problemOrFeeling"
        />
      </div>
      <div class="flex justify-center pb-3">
        <button
          type="button"
          data-modal-target="report-preview-modal"
          data-modal-toggle="report-preview-modal"
          class="mt-3 px-3 py-2 text-sm font-medium text-center inline-flex items-center text-white bg-green-700 rounded-lg
          hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700
          dark:focus:ring-green-800"
          :disabled="reports.length <= 0"
        >
          <svg class="w-3 h-3 me-1.5 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 14">
            <g stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
              <path d="M10 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
              <path d="M10 13c4.97 0 9-2.686 9-6s-4.03-6-9-6-9 2.686-9 6 4.03 6 9 6Z"/>
            </g>
          </svg>
          Preview
        </button>
        <button
          type="button"
          class="mt-3 ms-3 px-3 py-2 text-sm font-medium text-center inline-flex items-center text-white bg-blue-700 rounded-lg
          hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700
          dark:focus:ring-blue-800"
          @click="sendReport"
          :disabled="isLoading"
        >
          <svg class="w-3 h-3 me-1.5 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17v1a.97.97 0 0 1-.933 1H1.933A.97.97 0 0 1 1 18V5.828a2 2 0 0 1 .586-1.414l2.828-2.828A2 2 0 0 1 5.828 1h8.239A.97.97 0 0 1 15 2M6 1v4a1 1 0 0 1-1 1H1m13.14.772 2.745 2.746M18.1 5.612a2.086 2.086 0 0 1 0 2.953l-6.65 6.646-3.693.739.739-3.692 6.646-6.646a2.087 2.087 0 0 1 2.958 0Z"/>
          </svg>
          Report
        </button>
      </div>
      <div
        id="report-preview-modal"
        tabindex="-1"
        aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center
        w-full md:inset-0 h-[calc(100%-1rem)] max-h-full"
      >
        <div class="relative p-4 w-full max-w-2xl max-h-full">
          <!-- Modal content -->
          <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
              <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                Report Preview
              </h3>
              <Button
                type="button"
                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8
                ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                data-modal-hide="report-preview-modal"
              >
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Close modal</span>
              </Button>
            </div>
            <!-- Modal body -->
            <div class="p-4 md:p-5 space-y-4">
              <ul class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <li>Date: {{ format(new Date(), 'dd/MM/yyyy') }}</li>
                <li>Name: {{ user.name }}</li>
                <li>
                  Project: {{ reports[0]?.project_name }}
                </li>
                <li>
                  【実績】
                  <div v-if="reports.length > 0" v-for="(report, index) in reports" :key="index">
                    ({{ index + 1 }})⇒
                    {{ report.task_title }}
                    {{ report.percentage ? `<${report.percentage}%完了>` : '' }}
                    {{ report.hour ? `(${report.hour}hr)` : '' }}
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>