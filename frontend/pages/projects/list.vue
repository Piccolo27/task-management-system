<script setup lang="ts">
import {Project} from "~/types/project";
import {useStorage} from "@vueuse/core";

const {
  getSessionStorage,
  clearSessionStorage,
  truncateString,
  compareStrings,
  compareNumbers,
  compareDates
} = useUtils()
const {$api} = useNuxtApp()

const successMessage = ref<string | null>(null)
const errorMessage = ref<string | null>(null)
const searchQueryByPjName = ref('')
const searchQueryByLanguage = ref('')
const sortingOrder = ref({field: 'project_id', direction: 'asc'})
const projects = ref<Project[]>([])
const page = ref(1)
const projectsPerPage = 5

const {data, error} = await $api.project.get()
if (error.value) errorMessage.value = error.value.data.error
if (data.value) projects.value = data.value.projects

const paginatedProjects: ComputedRef<Project[]> = computed(() => {
  const startIndex = (page.value - 1) * projectsPerPage
  const endIndex = startIndex + projectsPerPage
  const filteredAndSortedProjects = getFilteredAndSortedProjects()
  return filteredAndSortedProjects.slice(startIndex, endIndex)
})

const getFilteredAndSortedProjects = () => {
  const filteredProjects = projects.value.filter(checkNameAndLang)
  const sortFunction = getSortFunction()
  return filteredProjects.sort(sortFunction)
}

const checkNameAndLang = (project: Project) => {
  const matchedPjName = !searchQueryByPjName.value
      || (project.project_name as string).toLowerCase().includes(searchQueryByPjName.value.toLowerCase());
  const matchedLanguage = !searchQueryByLanguage.value
      || (project.language as string).toLowerCase().includes(searchQueryByLanguage.value.toLowerCase())

  return matchedPjName && matchedLanguage;
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
  project_id: compareNumbers,
  project_name: compareStrings,
  language: compareStrings,
  start_date: compareDates,
  end_date: compareDates
}

const clearSuccessMessage = () => {
  clearSessionStorage('project-success')
  successMessage.value = null
}

const clearSearchQueries = () => {
  searchQueryByPjName.value = ''
  searchQueryByLanguage.value = ''
}

const changeSortingOrderByField = (field: string) => {
  const direction = sortingOrder.value.direction === 'asc' ? 'desc' : 'asc'
  sortingOrder.value.field = field
  sortingOrder.value.direction = direction
}

const deleteProject = async (projectId: string) => {
  const projectDeleteConfirm = window.confirm("Are you sure to delete?")

  if (projectDeleteConfirm) {
    const {data, error} = await $api.project.delete(projectId)
    if (error.value) errorMessage.value = error.value.data.error
    if (data.value) {
      useStorage('project-success', data.value.message, sessionStorage)
      removeDeletedProject(projectId)
    }
  }
}

const removeDeletedProject = (projectId: string) => {
  projects.value = projects.value.filter((project) => project.project_id != parseInt(projectId))
}

onMounted(() => successMessage.value = getSessionStorage('project-success'))
onBeforeUnmount(() => clearSuccessMessage())
</script>

<template>
  <section class="px-2">
    <SuccessAlert v-if="successMessage" class="w-1/3" @dismiss="clearSuccessMessage">{{ successMessage }}</SuccessAlert>
    <ErrorAlert v-if="errorMessage" class="w-1/3" @dismiss="() => errorMessage = null">{{ errorMessage }}</ErrorAlert>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg py-7">
      <div class="flex justify-between">
        <div class="flex ps-5">
          <div class="pb-4 flex items-center mr-3">
            <label for="table-search" class="mr-3">Project Name: </label>
            <input
                type="text"
                id="table-search"
                v-model="searchQueryByPjName"
                class="block p-2 text-sm text-gray-900 border border-gray-300 rounded-lg w-60 bg-gray-50
                focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600
                dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            />
          </div>
          <div class="pb-4 flex items-center">
            <label for="table-search" class="mr-3">Language: </label>
            <input
                type="text"
                id="table-search"
                v-model="searchQueryByLanguage"
                class="block p-2 text-sm text-gray-900 border border-gray-300 rounded-lg w-60 bg-gray-50
                focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600
                dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            />
          </div>
          <div class="ml-4">
            <button
                type="button"
                @click="clearSearchQueries"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium
                rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none
                dark:focus:ring-blue-800"
            >
              Clear
            </button>
          </div>
        </div>
        <div>
          <NuxtLink
              to="/projects/create"
              type="button"
              class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4
              focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 mb-2
              dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800"
          >
            New Project
          </NuxtLink>
        </div>
      </div>
      <Table>
        <template #table-header>
          <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
          <tr>
            <th scope="col" class="px-6 py-3">
              <div class="flex items-center">
                Project ID
                <FilterArrows @click="changeSortingOrderByField('project_id')"/>
              </div>
            </th>
            <th scope="col" class="px-6 py-3">
              <div class="flex items-center">
                Project Name
                <FilterArrows @click="changeSortingOrderByField('project_name')"/>
              </div>
            </th>
            <th scope="col" class="px-6 py-3">
              <div class="flex items-center">
                Language
                <FilterArrows @click="changeSortingOrderByField('language')"/>
              </div>
            </th>
            <th scope="col" class="px-6 py-3">
              Description
            </th>
            <th scope="col" class="px-6 py-3">
              <div class="flex items-center">
                Start Date
                <FilterArrows @click="changeSortingOrderByField('start_date')"/>
              </div>
            </th>
            <th scope="col" class="px-6 py-3">
              <div class="flex items-center">
                End Date
                <FilterArrows @click="changeSortingOrderByField('end_date')"/>
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
              v-if="paginatedProjects.length > 0"
              v-for="project in paginatedProjects"
              :key="project.project_id"
              class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50"
          >
            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
              {{ project.project_id }}
            </th>
            <td class="px-6 py-4">
              {{ project.project_name }}
            </td>
            <td class="px-6 py-4">
              {{ project.language }}
            </td>
            <td class="px-6 py-4">
              {{ project.description ? truncateString(project.description) : '' }}
            </td>
            <td class="px-6 py-4">
              {{ project.start_date }}
            </td>
            <td class="px-6 py-4">
              {{ project.end_date }}
            </td>
            <td class="px-6 py-4">
              <NuxtLink
                  :to="`/projects/${project.project_id}/edit`"
                  class="font-medium text-blue-600 dark:text-blue-500 hover:underline mx-3">
                Edit
              </NuxtLink>
              <button
                  @click="deleteProject(project.project_id)"
                  class="font-medium text-red-600 dark:text-red-500 hover:underline">
                Delete
              </button>
            </td>
          </tr>
          <div v-else class="ps-5 py-5 w-full text-red-500 whitespace-nowrap">
            There is no project
          </div>
          </tbody>
        </template>
      </Table>
      <UPagination
          v-if="projects.length > 5"
          class="mt-3 ms-4"
          v-model="page"
          :page-count="projectsPerPage"
          :total="projects.length"
      />
    </div>
  </section>
</template>