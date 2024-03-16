<script setup lang="ts">

import {DM} from '~/types/dm'
import {Employee} from "~/types/employee";

const {getSessionStorage, clearSessionStorage} = useUtils()
const {$api} = useNuxtApp()
const user: Employee = useCookie('user').value as unknown as Employee

const successMessage = ref<string | null>(null)
const directMessages = ref<DM[]>([])
const page = ref(1)
const dmPerPage = 5
const errorMessage = ref<string | null>(null)

const paginatedDMs = computed(() => {
  const startIndex = (page.value - 1) * dmPerPage
  const endIndex = startIndex + dmPerPage

  const filteredDms = directMessages.value.filter((dm) => {
    if (dm.owner.employee_id == user.id) {
      return true;
    } else {
      let startDate = new Date(dm.start_at)
      return startDate <= new Date()
    }
  })

  return filteredDms.slice(startIndex, endIndex);
})

const {data, error} = await $api.dm.getAll()
if (error.value) errorMessage.value = error.value.data.error
if (data.value) directMessages.value = data.value.dms

const clear = () => {
  clearSessionStorage('employee-success')
  successMessage.value = null
}

const isDateTodayOrYesterday = (dateString: string) => {
  const createdDate = new Date(dateString);
  const today = new Date();
  const yesterday = new Date(today);

  yesterday.setDate(yesterday.getDate() - 1);

  return createdDate.toDateString() === today.toDateString() ||
      createdDate.toDateString() === yesterday.toDateString();
}

onMounted(() => {
  successMessage.value = getSessionStorage('employee-success')
})

onUnmounted(() => clear())
</script>

<template>
  <section class="px-6">
    <SuccessAlert v-if="successMessage" class="w-1/3" @dismiss="clear">{{ successMessage }}</SuccessAlert>
    <ErrorAlert v-if="errorMessage" class="w-1/3" @dismiss="() => errorMessage = null">{{ errorMessage }}</ErrorAlert>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg py-7">
      <div class="flex justify-between">
        <div>
          <NuxtLink
              to="/dm/create"
              type="button"
              class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4
              focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 mb-4 
              dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800"
          >
            New DM
          </NuxtLink>
        </div>
      </div>
      <Table>
        <template #table-header>
          <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
          <tr>
            <th scope="col" class="px-6 py-3">
              No.
            </th>
            <th scope="col" class="px-6 py-3">
              DM Sender
            </th>
            <th scope="col" class="px-6 py-3">
              Title
            </th>
            <th scope="col" class="px-6 py-3">
              Status
            </th>
            <th scope="col" class="px-6 py-3"></th>
          </tr>
          </thead>
        </template>
        <template #table-body>
          <tbody>
          <tr
              v-if="directMessages.length > 0"
              v-for="(dm, index) in paginatedDMs"
              :key="dm.direct_message_id"
              class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
          >
            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
              {{ index + 1 }}
            </th>
            <td class="px-6 py-4">
              {{ dm.owner.employee_name }}
            </td>
            <td class="px-6 py-4">
              {{ dm.title }}
            </td>
            <td class="px-6 py-4">
              <button
                  v-if="isDateTodayOrYesterday(dm.created_at)"
                  type="button"
                  class="text-green-700 border border-green-700 font-medium rounded-lg text-sm px-5 py-2.5
                      text-center me-2 mb-2 dark:border-green-500 dark:text-green-500 cursor-default"
              >
                New
              </button>
            </td>
            <td class="px-6 py-4">
              <NuxtLink
                  v-if="new Date(dm.start_at) <= new Date()"
                  :to="`/dm/${dm.direct_message_id}/detail`"
                  class="text-blue-600 dark:text-blue-500 hover:underline">
                details
              </NuxtLink>
              <NuxtLink
                  v-else
                  :to="`/dm/${dm.direct_message_id}/edit`"
                  class="text-blue-600 dark:text-blue-500 hover:underline">
                update
              </NuxtLink>
            </td>
          </tr>
          </tbody>
        </template>
      </table>
      <UPagination
        v-if="directMessages.length> 5"
        class="mt-3 ms-4"
        v-model="page"
        :page-count="dmPerPage"
        :total="directMessages.length"
      />
    </div>
  </section>
</template>