<script setup lang="ts">
import {onMounted} from 'vue';
import {initDropdowns} from 'flowbite';
import {formatDistanceToNow, parseISO} from "date-fns";
import {Notification} from "~/types/notification";
import {Employee} from "~/types/employee";
import {ADMIN} from "~/constants/constants";

const dropDownItemClass = 'block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white'

const {$api, $echo} = useNuxtApp()
const config = useRuntimeConfig()
const storagePath = config.public.storagePath

const {logout} = useUtils()
const user: Employee = useCookie('user').value as unknown as Employee

const navItems = {
  employee: {
    button: {
      text: 'Employee',
      dropdownId: 'dropdownEmployee',
    },
    dropdownItems: [
      {
        linkTo: '/employees/create',
        class: dropDownItemClass,
        text: 'New Employee'
      },
      {
        linkTo: '/employees/list',
        class: dropDownItemClass,
        text: 'Employee List'
      }
    ],
    isShow: (parseInt(user.position as string) == ADMIN)
  },
  project: {
    button: {
      text: 'Project',
      dropdownId: 'dropdownProject',
    },
    dropdownItems: [
      {
        linkTo: '/projects/create',
        class: dropDownItemClass,
        text: 'New Project'
      },
      {
        linkTo: '/projects/list',
        class: dropDownItemClass,
        text: 'Project List'
      }
    ],
    isShow: (parseInt(user.position as string) == ADMIN)
  },
  task: {
    button: {
      'text': 'Task',
      dropdownId: 'dropdownTask',
    },
    dropdownItems: [
      {
        linkTo: '/tasks/create',
        class: dropDownItemClass,
        text: 'New Task'
      },
      {
        linkTo: '/tasks/list',
        class: dropDownItemClass,
        text: 'Task List'
      }
    ],
    isShow: true
  },
  report: {
    button: {
      'text': 'Report',
      dropdownId: 'dropdownReport',
    },
    dropdownItems: [
      {
        linkTo: '/reports/create',
        class: dropDownItemClass,
        text: 'New Report'
      },
      {
        linkTo: '/reports/list',
        class: dropDownItemClass,
        text: 'Report List'
      }
    ],
    isShow: true
  },
  dm: {
    button: {
      'text': 'DM',
      dropdownId: 'dropdownDM',
    },
    dropdownItems: [
      {
        linkTo: '/dm/create',
        class: dropDownItemClass,
        text: 'New DM'
      },
      {
        linkTo: '/dm/list',
        class: dropDownItemClass,
        text: 'DM List'
      }
    ],
    isShow: true
  }
}

const router = useRouter()
const notifications = ref<Notification[]>([])
const isNewNoti = ref(false)
const errorMessage = ref('')

const { data, error } = await $api.notification.getByEmployeeId(user.id as string)
if (error.value) errorMessage.value = error.value?.data.error
if (data.value) notifications.value = data.value.notifications

const handleNotificationEvent = (event: string, data: { noti: Notification }) => {
  if (
      event === '.employee-created' ||
      event === '.employee-updated' ||
      event === '.employee-deleted' ||
      event === '.project-created'  ||
      event === '.project-updated'  ||
      event === '.project-deleted'
  ) {
    if (user.id != data.noti.created_by) {
      notifications.value.unshift(data.noti)
      isNewNoti.value = true
    }
  } else if (event === '.employee-reported') {
    if (user.id != data.noti.created_by && user.id == data.noti.report_to) {
      notifications.value.unshift(data.noti)
      isNewNoti.value = true
    }
  } else if (event === '.task-created' || event === '.task-updated') {
    if (
        user.id != data.noti.created_by &&
        parseInt(user.position as string) == ADMIN ||
        user.id == data.noti.task_member_id
    ) {
      notifications.value.unshift(data.noti)
      isNewNoti.value = true
    }
  }
}

const listenNotiEventsOnChannel = () => {
  if (parseInt(user.position as string) === ADMIN) {
    $echo.private('admin-notification')
      .listen('.employee-created', (data: { noti: Notification }) => {
        handleNotificationEvent('.employee-created', data)
      })
      .listen('.employee-updated', (data: { noti: Notification }) => {
        handleNotificationEvent('.employee-updated', data)
      })
      .listen('.employee-deleted', (data: { noti: Notification }) => {
        handleNotificationEvent('.employee-deleted', data)
      })
      .listen('.project-created', (data: { noti: Notification }) => {
        handleNotificationEvent('.project-created', data)
      })
      .listen('.project-updated', (data: { noti: Notification }) => {
        handleNotificationEvent('.project-updated', data)
      })
      .listen('.project-deleted', (data: { noti: Notification }) => {
        handleNotificationEvent('.project-deleted', data)
      })
      .listen('.employee-reported', (data: { noti: Notification }) => {
        handleNotificationEvent('.employee-reported', data)
      })
  }

  $echo.private('task-notification')
    .listen('.task-created', (data: {noti: Notification}) => {
      handleNotificationEvent('.task-created', data)
    })
    .listen('.task-updated', (data: {noti: Notification}) => {
      handleNotificationEvent('.task-updated', data)
    })
}

const deleteNoti = async (notiId: string) => {
  const notiDeleteConfirm = window.confirm("Are you sure to delete?")

  if (notiDeleteConfirm) {
    const {data, error} = await $api.notification.delete(notiId, user.id as string)
    if (error.value) errorMessage.value = error.value?.data.error
    if (data.value) notifications.value = notifications.value.filter(noti => noti.id != parseInt(notiId))
  }
}

const toProfilePage = () => {
  router.push(`/employees/${user.id}`)
}

onMounted(() => {
  initDropdowns()
  listenNotiEventsOnChannel()
})

onBeforeUnmount(() => {
  $echo.leaveChannel('admin-notification')
})
</script>

<template>
  <nav class="bg-white border-gray-200 dark:bg-gray-800">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
      <NuxtLink to="/" class="flex items-center">
        <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">
          Task Management System
        </span>
      </NuxtLink>
      <div class="flex items-center md:order-3">
        <button type="button" class="flex mr-3 text-sm bg-gray-800 rounded-full md:mr-0 focus:ring-4 focus:ring-gray-300
                  dark:focus:ring-gray-600" id="user-menu-button" aria-expanded="false"
                data-dropdown-toggle="user-dropdown" data-dropdown-placement="bottom">
          <span class="sr-only">Open user menu</span>
          <img class="w-8 h-8 rounded-full" :src="storagePath + user.profile" alt="user photo">
        </button>
        <!-- Dropdown menu -->
        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow
              dark:bg-gray-700 dark:divide-gray-600" id="user-dropdown">
          <div class="px-4 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" @click="toProfilePage">
            <span class="block text-sm text-gray-900 dark:text-white">{{ user.name }}</span>
            <span class="block text-sm  text-gray-500 truncate dark:text-gray-400">{{ user.email }}</span>
          </div>
          <ul class="py-2" aria-labelledby="user-menu-button">
            <li>
              <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600
                  dark:text-gray-200 dark:hover:text-white" @click="logout">
                Logout
              </a>
            </li>
          </ul>
        </div>
        <button data-collapse-toggle="navbar-user" type="button"
          class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden
            hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400
            dark:hover:bg-gray-700 dark:focus:ring-gray-600"
          aria-controls="navbar-user" aria-expanded="false"
        >
          <span class="sr-only">Open main menu</span>
          <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M1 1h15M1 7h15M1 13h15"/>
          </svg>
        </button>
      </div>
      <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-user">
        <ul class="flex flex-col font-medium p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:flex-row
          md:space-x-8 md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:8 dark:border-gray-700">
          <NavItem
              :button="navItems.employee.button"
              :dropdown-items="navItems.employee.dropdownItems"
              :is-show="navItems.employee.isShow"
          />
          <NavItem
              :button="navItems.project.button"
              :dropdown-items="navItems.project.dropdownItems"
              :is-show="navItems.project.isShow"
          />
          <NavItem
              :button="navItems.task.button"
              :dropdown-items="navItems.task.dropdownItems"
              :is-show="navItems.task.isShow"
          />
          <NavItem
              :button="navItems.report.button"
              :dropdown-items="navItems.report.dropdownItems"
              :is-show="navItems.report.isShow"
          />
          <NavItem
              :button="navItems.dm.button"
              :dropdown-items="navItems.dm.dropdownItems"
              :is-show="navItems.dm.isShow"
          />
        </ul>
      </div>
      <div class="md:order-2">
        <LightDarkSwitchBtn/>
      </div>
      <div class="md:order-3 flex items-center">
        <button @click="isNewNoti = false" id="dropdownNotificationButton"
                data-dropdown-toggle="dropdownNotification"
                class="relative inline-flex items-center text-sm font-medium text-center text-gray-500 hover:text-gray-900 focus:outline-none dark:hover:text-white dark:text-gray-400"
                type="button"
        >
          <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
               viewBox="0 0 14 20">
            <path
                d="M12.133 10.632v-1.8A5.406 5.406 0 0 0 7.979 3.57.946.946 0 0 0 8 3.464V1.1a1 1 0 0 0-2 0v2.364a.946.946 0 0 0 .021.106 5.406 5.406 0 0 0-4.154 5.262v1.8C1.867 13.018 0 13.614 0 14.807 0 15.4 0 16 .538 16h12.924C14 16 14 15.4 14 14.807c0-1.193-1.867-1.789-1.867-4.175ZM3.823 17a3.453 3.453 0 0 0 6.354 0H3.823Z"/>
          </svg>
          <div v-if="isNewNoti"
               class="absolute block w-3 h-3 bg-red-500 border-2 border-white rounded-full -top-0.5 start-2.5 dark:border-gray-900"></div>
        </button>

        <!-- Dropdown menu -->
        <div id="dropdownNotification"
             class="z-20 hidden w-full max-w-sm bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-800 dark:divide-gray-700"
             aria-labelledby="dropdownNotificationButton">
          <div
              class="block px-4 py-2 font-medium text-center text-gray-700 rounded-t-lg bg-gray-50 dark:bg-gray-800 dark:text-white">
            Notifications
          </div>
          <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-[440px] overflow-y-auto">
            <div v-if="notifications.length <= 0"
                 class="px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 text-sm mb-1.5 dark:text-gray-400"
            >
              There is no notification
            </div>
            <div
                v-for="(notification, index) in notifications"
                :key="index"
                class="flex px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 group">
              <div class="flex-shrink-0">
                <img class="rounded-full w-11 h-11" :src="storagePath + notification.created_employee?.profile" alt="Profile">
              </div>
              <div class="w-full ps-3">
                <div class="text-gray-500 text-sm mb-1.5 dark:text-gray-400">
                  {{ notification.message }}
                </div>
                <div class="text-xs text-blue-600 dark:text-blue-500 flex items-center justify-between">
                  <span>{{ formatDistanceToNow(parseISO(notification.created_at), {addSuffix: true}) }}</span>
                  <span @click="deleteNoti(notification.id as string)" class="cursor-pointer hidden group-hover:block">
                    <svg class="w-3 h-3 text-gray-800 dark:text-red-700" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 20">
                      <path
                          stroke="currentColor"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M1 5h16M7 8v8m4-8v8M7 1h4a1 1 0 0 1 1 1v3H6V2a1 1 0 0 1 1-1ZM3 5h12v13a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V5Z"
                      />
                    </svg>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>