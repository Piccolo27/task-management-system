<script setup lang="ts">
import {ProjectValidationError, Project} from "~/types/project";
import {useStorage} from "@vueuse/core";
import {projectInputs, projectSubmitBtn} from "~/constants/formInputs";

const {$api} = useNuxtApp()
const route = useRoute()

const startDate = ref<Date|null>(null)
const endDate = ref<Date|null >(null)
const isLoading = ref(false)
const errorMessage = ref<string | null>(null)

const project = reactive<Project>({
  project_id: 0,
  project_name: '',
  language: '',
  description: ''
})

const validationErrors = reactive<ProjectValidationError>({
  project_name: '',
  language: '',
  description: '',
  start_date: '',
  end_date: ''
})

const projectId = computed(() => route.params.id)

const setProject = (data: Project) => {
  project.project_id = data.project_id
  project.project_name = data.project_name
  project.language = data.language
  project.description = data.description ?? ''
  startDate.value = data.start_date ? new Date(data.start_date) : null
  endDate.value = data.end_date ? new Date(data.end_date) : null
}

const { data, error } = await $api.project.getOne(projectId.value as string)
if (error.value) errorMessage.value = error.value.data.error
if (data.value) setProject(data.value.project)

const handleSubmit = async () => {
  isLoading.value = true
  clearMessages()

  const projectData = getProjectDate()
  const { data, error } = await $api.project.update(projectData)

  if (error.value) {
    setErrorMessages(error.value)
    isLoading.value = false
    return
  }

  if (data.value) {
    useStorage('project-success', data.value.message, sessionStorage)
    isLoading.value = false
    return navigateTo('/projects/list')
  }
}

const getProjectDate = () => {
  return {
    'project_id': project.project_id,
    'project_name': project.project_name,
    'language': project.language,
    'description': project.description,
    'start_date': startDate.value
        ? (startDate.value as Date).toISOString().split('T')[0]
        : null,
    'end_date': endDate.value
        ? (endDate.value as Date).toISOString().split('T')[0]
        : null,
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

const changeStartDate = (newState: Date) => startDate.value = newState
const changeEndDate = (newState: Date) => endDate.value = newState

const clearMessages = () => {
  errorMessage.value = null
  for (const field of Object.keys(validationErrors)) {
    (validationErrors as any)[field] = null
  }
}
</script>

<template>
  <section class="bg-white dark:bg-gray-900">
    <ProjectForm
      form-title="Edit Project"
      :button="projectSubmitBtn"
      :btn-disabled="isLoading"
      @form-submit="handleSubmit"
    >
      <template #errorNoti>
        <ErrorAlert v-if="errorMessage" @dismiss="clearMessages">{{ errorMessage }}</ErrorAlert>
      </template>
      <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
        <div class="sm:col-span-2">
          <InputGroup
            v-model="project.project_name"
            :input="projectInputs.name"
            :error-message="validationErrors.project_name"
          />
        </div>
        <div class="sm:col-span-2">
          <InputGroup
              v-model="project.language"
              :input="projectInputs.language"
              :error-message="validationErrors.language"
          />
        </div>
        <div class="sm:col-span-2">
          <InputGroup
              v-model="project.description"
              :input="projectInputs.description"
              :error-message="validationErrors.description"
          />
        </div>
        <div class="sm:col-span-2">
          <InputGroup
              :input="projectInputs.startDate"
              :error-message="validationErrors.start_date"
          >
            <template #date>
              <DatePicker v-model="startDate" @change-date-time="changeStartDate" :enable-time-picker="false" />
            </template>
          </InputGroup>
        </div>
        <div class="sm:col-span-2">
          <InputGroup
              :input="projectInputs.endDate"
              :error-message="validationErrors.end_date"
          >
            <template #date>
              <DatePicker v-model="endDate" @change-date-time="changeEndDate" :enable-time-picker="false" />
            </template>
          </InputGroup>
        </div>
      </div>
    </ProjectForm>
  </section>
</template>