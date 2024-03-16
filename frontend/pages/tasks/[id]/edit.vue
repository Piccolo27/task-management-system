<script setup lang="ts">
import {Task, TaskValidationError} from "~/types/task";
import {Project} from "~/types/project";
import {Employee} from "~/types/employee";
import {useStorage} from "@vueuse/core";

const {$api} = useNuxtApp()
const route = useRoute()
const {formatDateTime} = useUtils()

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
      isRequired: false
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
  actualHr: {
    type: 'text',
    name: 'actual_hr',
    id: 'actual_hr',
    class: inputClass,
    label: {
      for: 'actual_hr',
      text: 'Actual Hour',
      isRequired: false
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
  },
  actualStartDate: {
    type: 'date',
    name: 'actual_end_date',
    id: 'actual_end_date',
    label: {
      for: 'actual_end_date',
      text: 'Actual Start(Date/Time)',
      isRequired: false
    }
  },
  actualEndDate: {
    type: 'date',
    name: 'actual_end_date',
    id: 'actual_end_date',
    label: {
      for: 'actual_end_date',
      text: 'Actual Finish(Date/Time)',
      isRequired: false
    }
  },
  status: {
    type: 'select',
    name: 'status',
    id: 'status',
    class: 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 ' +
        'block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white ' +
        'dark:focus:ring-blue-500 dark:focus:border-blue-500',
    label: {
      for: 'status',
      text: 'Status',
      isRequired: true
    },
    selectOptions: [{0: 'open'}, {1: 'in progress'}, {2: 'finish'}, {3: 'close'}]
  },
})

const estimateStartDate = ref<Date|null>(null)
const estimateFinishDate = ref<Date|null >(null)
const actualStartDate = ref<Date|null>(null)
const actualFinishDate = ref<Date|null >(null)
const isLoading = ref(false)
const errorMessage = ref<string | null>(null)

const task = reactive<Task>({
  project_id: null,
  title: '',
  description: '',
  assigned_member_id: null,
  estimate_hr: null,
  actual_hr: null,
  status: null,
  project: {},
  member: {}
})

const validationErrors = reactive<TaskValidationError>({
  project_id: null,
  title: null,
  description: null,
  assigned_member_id: null,
  estimate_hr: null,
  actual_hr: null,
  estimate_start_date: null,
  estimate_finish_date: null,
  status: null,
  actual_start_date: null,
  actual_finish_date: null
})

const taskId = computed(() => route.params.id)

const loadData = async (apiCall: Function, successCallback: Function, ...params: any[]) => {
  const { data, error } = await apiCall(...params);
  if (data.value) {
    successCallback(data.value);
  }
  if (error.value) {
    errorMessage.value = error.value?.data.error || 'An unknown error occurred';
  }
}

loadData($api.project.get, (projectData: any) => {
  projectData.projects.forEach((project: any) => {
    const data = { [project.project_id.toString()]: project.project_name };
    inputs.value.project.selectOptions.push(data as never);
  });
});

loadData($api.employee.getEmployees, (employeeData: any) => {
  employeeData.employees.forEach((employee: Employee) => {
    const data = { [employee.employee_id as string]: employee.employee_name };
    inputs.value.assignEmployee.selectOptions.push(data as never);
  });
});

loadData($api.task.getOne, (taskData: any) => {
  setTask(taskData.task)
}, taskId.value)

const setTask = (data: Task) => {
  task.project_id = data.project_id
  task.title = data.title
  task.description = data.description
  task.assigned_member_id = data.assigned_member_id
  task.estimate_hr = data.estimate_hr
  task.actual_hr = data.actual_hr
  task.status = data.status
  task.project = data.project
  task.member = data.member

  estimateStartDate.value = data.estimate_start_date ? new Date(data.estimate_start_date) : null
  estimateFinishDate.value = data.estimate_finish_date ? new Date(data.estimate_finish_date) : null
  actualStartDate.value = data.actual_start_date ? new Date(data.actual_start_date) : null
  actualFinishDate.value = data.actual_finish_date ? new Date(data.actual_finish_date) : null
}

const handleSubmit = async () => {
  isLoading.value = true
  clearMessages()

  const taskData = getTaskData()
  const { data, error } = await $api.task.update(taskId.value as string, taskData)

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
    actual_hr: task.actual_hr,
    status: task.status,
    estimate_start_date: estimateStartDate.value != null
        ? formatDateTime(estimateStartDate.value as Date)
        : null,
    estimate_finish_date: estimateStartDate.value != null
        ? formatDateTime(estimateFinishDate.value as Date)
        : null,
    actual_start_date: actualStartDate.value != null
        ? formatDateTime(actualStartDate.value as Date)
        : null,
    actual_finish_date: actualStartDate.value != null
        ? formatDateTime(actualFinishDate.value as Date)
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
const changeEstimateFinishDate = (newState: Date) => estimateFinishDate.value = newState
const changeActualStartDate = (newState: Date) => actualStartDate.value = newState
const changeActualFinishDate = (newState: Date) => actualFinishDate.value = newState
</script>

<template>
  <section class="bg-white dark:bg-gray-900">
    <TaskForm
      form-title="Edit Task"
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
              <DatePicker
                v-model="estimateStartDate"
                @change-date-time="changeEstimateStartDate"
                :enable-time-picker="true"
              />
            </template>
          </InputGroup>
        </div>
        <div class="sm:col-span-2">
          <InputGroup
            :input="inputs.estimateEndDate"
            :error-message="validationErrors.estimate_finish_date"
          >
            <template #date>
              <DatePicker
                v-model="estimateFinishDate"
                @change-date-time="changeEstimateFinishDate"
                :enable-time-picker="true"
              />
            </template>
          </InputGroup>
        </div>
        <div class="sm:col-span-2">
          <InputGroup
            v-model="task.status"
            :input="inputs.status"
            :error-message="validationErrors.status"
          />
        </div>
        <div class="sm:col-span-2">
          <InputGroup
            v-model="task.actual_hr"
            :input="inputs.actualHr"
            :error-message="validationErrors.actual_hr"
          />
        </div>
        <div class="sm:col-span-2">
          <InputGroup
            :input="inputs.actualStartDate"
            :error-message="validationErrors.actual_start_date"
          >
            <template #date>
              <DatePicker
                v-model="actualStartDate"
                @change-date-time="changeActualStartDate"
                :enable-time-picker="true"
              />
            </template>
          </InputGroup>
        </div>
        <div class="sm:col-span-2">
          <InputGroup
            :input="inputs.actualEndDate"
            :error-message="validationErrors.actual_finish_date"
          >
            <template #date>
              <DatePicker
                v-model="actualFinishDate"
                @change-date-time="changeActualFinishDate"
                :enable-time-picker="true"
              />
            </template>
          </InputGroup>
        </div>
      </div>
    </TaskForm>
  </section>
</template>