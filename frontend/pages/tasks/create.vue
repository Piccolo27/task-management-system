<script setup lang="ts">
import {Task, TaskValidationError} from "~/types/task";
import {Project} from "~/types/project";
import {Employee} from "~/types/employee";
import {useStorage} from "@vueuse/core";

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

const inputs = ref({
  project: {
    type: 'select',
    name: 'project',
    id: 'project',
    class: 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 ' +
        'block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white ' +
        'dark:focus:ring-blue-500 dark:focus:border-blue-500',
    label: {
      for: 'project',
      text: 'Project',
      isRequired: true
    },
    selectOptions: []
  },
  title: {
    type: 'text',
    name: 'title',
    id: 'title',
    class: inputClass,
    label: {
      for: 'title',
      text: 'Title',
      isRequired: true
    }
  },
  description: {
    type: 'textarea',
    name: 'description',
    id: 'description',
    class: 'block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 ' +
        'focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white ' +
        'dark:focus:ring-blue-500 dark:focus:border-blue-500',
    label: {
      for: 'description',
      text: 'Description',
      isRequired: true
    }
  },
  assignEmployee: {
    type: 'select',
    name: 'assign_employee',
    id: 'assign_employee',
    class: 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 ' +
        'block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white ' +
        'dark:focus:ring-blue-500 dark:focus:border-blue-500',
    label: {
      for: 'assign_employee',
      text: 'Assign Employee',
      isRequired: true
    },
    selectOptions: []
  },
  estimateHr: {
    type: 'text',
    name: 'estimate_hr',
    id: 'estimate_hr',
    class: inputClass,
    label: {
      for: 'estimate_hr',
      text: 'Estimate Hour',
      isRequired: true
    }
  },
  estimateStartDate: {
    type: 'date',
    name: 'estimate_start_date',
    id: 'estimate_start_date',
    label: {
      for: 'estimate_start_date',
      text: 'Estimate Start(Date/Time)',
      isRequired: false
    }
  },
  estimateEndDate: {
    type: 'date',
    name: 'estimate_end_date',
    id: 'estimate_end_date',
    label: {
      for: 'estimate_end_date',
      text: 'Estimate Finish(Date/Time)',
      isRequired: false
    }
  }
})

const estimateStartDate = ref<Date|null>(null)
const estimateFinishDate = ref<Date|null >(null)
const isLoading = ref(false)
const errorMessage = ref<string | null>(null)

const task = reactive<Task>({
  project_id: null,
  title: '',
  description: '',
  assigned_member_id: null,
  estimate_hr: null
})

const validationErrors = reactive<TaskValidationError>({
  project_id: null,
  title: null,
  description: null,
  assigned_member_id: null,
  estimate_hr: null,
  estimate_start_date: null,
  estimate_finish_date: null
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

loadData($api.project.get, (projectData: any) => {
  projectData.projects.forEach((project: any) => {
    const data = { [project.project_id.toString()]: project.project_name }
    inputs.value.project.selectOptions.push(data as never)
  });
});

loadData($api.employee.getEmployees, (employeeData: any) => {
  employeeData.employees.forEach((employee: Employee) => {
    const data = { [employee.employee_id as string]: employee.employee_name }
    inputs.value.assignEmployee.selectOptions.push(data as never)
  });
});

const handleSubmit = async () => {
  isLoading.value = true
  clearMessages()

  const taskData = getTaskData()
  const { data, error } = await $api.task.create(taskData)

  if (error.value) {
    setErrorMessages(error.value)
    isLoading.value = false
    return
  }

  if (data.value) {
    useStorage('task-success', data.value.message, sessionStorage)
    isLoading.value = false
    return navigateTo('/tasks/list')
  }
}

const getTaskData = () => {
  return {
    project_id: task.project_id,
    title: task.title,
    description: task.description,
    assigned_member_id: task.assigned_member_id,
    estimate_hr: task.estimate_hr,
    estimate_start_date: estimateStartDate.value != null
        ? (estimateStartDate.value as Date).toISOString().split('T')[0]
        : null,
    estimate_finish_date: estimateStartDate.value != null
        ? (estimateFinishDate.value as Date).toISOString().split('T')[0]
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

const changeEstimateStartDate = (newState: Date) => estimateStartDate.value = newState
const changeEstimateEndDate = (newState: Date) => estimateFinishDate.value = newState
</script>

<template>
  <section class="bg-white dark:bg-gray-900">
    <TaskForm
        form-title="New Task"
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
              v-model="task.project_id"
              :input="inputs.project"
              :error-message="validationErrors.project_id"
          />
        </div>
        <div class="sm:col-span-2">
          <InputGroup
              v-model="task.title"
              :input="inputs.title"
              :error-message="validationErrors.title"
          />
        </div>
        <div class="sm:col-span-2">
          <InputGroup
              v-model="task.description"
              :input="inputs.description"
              :error-message="validationErrors.description"
          />
        </div>
        <div class="sm:col-span-2">
          <InputGroup
              v-model="task.assigned_member_id"
              :input="inputs.assignEmployee"
              :error-message="validationErrors.assigned_member_id"
          />
        </div>
        <div class="sm:col-span-2">
          <InputGroup
              v-model="task.estimate_hr"
              :input="inputs.estimateHr"
              :error-message="validationErrors.estimate_hr"
          />
        </div>
        <div class="sm:col-span-2">
          <InputGroup
              :input="inputs.estimateStartDate"
              :error-message="validationErrors.estimate_start_date"
          >
            <template #date>
              <DatePicker @change-date-time="changeEstimateStartDate" :enable-time-picker="true" />
            </template>
          </InputGroup>
        </div>
        <div class="sm:col-span-2">
          <InputGroup
              :input="inputs.estimateEndDate"
              :error-message="validationErrors.estimate_finish_date"
          >
            <template #date>
              <DatePicker @change-date-time="changeEstimateEndDate" :enable-time-picker="true" />
            </template>
          </InputGroup>
        </div>
      </div>
    </TaskForm>
  </section>
</template>