<script setup lang="ts">
import {DM, DMReplyCreate, DMReplyUpdate} from '~/types/dm';
import {formatDistanceToNow, parseISO} from "date-fns";
import {REPLYABLE} from "~/constants/constants";
import {AuthUser} from "~/types/auth";
import {integer} from "vscode-languageserver-types";

const route = useRoute()
const {$api, $echo} = useNuxtApp()
const config = useRuntimeConfig()
const storagePath = config.public.storagePath
const user = useCookie('user').value as any as AuthUser
const editBtn = {
  type: 'submit',
  class: 'inline-flex items-center py-2.5 px-4 text-xs font-medium text-center text-white bg-blue-700 rounded-lg ' +
      'focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900 hover:bg-blue-800'
}
const cancelBtn = {
  type: 'button',
  class: 'ms-3 focus:outline-none text-white bg-red-700 text-xs hover:bg-red-800 focus:ring-4 focus:ring-red-300 ' +
      'font-medium rounded-lg px-4 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900'
}

const isLoading = ref(false)
const errorMessage = ref<string | null>(null)
const replyErrorMessage = ref<string | null>(null)
const dmReply = ref('')
const replyTxtArea = ref<any>(null)
const isEditing = ref(false)
const editingReplyId = ref<integer | null>(null)

const dmID = computed(() => route.params.id)

const {data, error} = await $api.dm.get(dmID.value as string)
if (error.value) errorMessage.value = error.value.data.error

const dm: ComputedRef<DM> = computed(() => data.value.dm[0])

const handleSubmit = async () => {
  if (!isEditing.value) {
    await createDmReply()
  } else {
    await updateDmReply()
  }
  dmReply.value = ''
}

const createDmReply = async () => {
  isLoading.value = true
  const dmReplyData: DMReplyCreate = {
    dm_thread_id: parseInt(dmID.value as string),
    body: dmReply.value
  }

  const {error} = await $api.dmReply.create(dmReplyData)
  if (error.value) replyErrorMessage.value = error.value.data.error
  isLoading.value = false
}

const updateDmReply = async () => {
  isLoading.value = true
  const dmReplyData: DMReplyUpdate = {
    dm_thread_id: parseInt(dmID.value as string),
    body: dmReply.value,
    dm_reply_id: editingReplyId.value as number
  }

  const {error} = await $api.dmReply.update(dmReplyData)
  if (error.value) replyErrorMessage.value = error.value.data.error
  isEditing.value = false
  isLoading.value = false
}

const editDmReply = (dmReplyText: string, dmReplyId: integer) => {
  isEditing.value = true

  editingReplyId.value = dmReplyId
  dmReply.value = dmReplyText
  replyTxtArea.value.scrollIntoView({behavior: 'smooth'});
  replyTxtArea.value.focus({preventScroll: true});
}

const cancelEdit = () => {
  isEditing.value = false
  dmReply.value = ''
  editingReplyId.value = null
}

const deleteDmReply = (dmReplyId: number) => {
  if (window.confirm("Are you sure to delete?")) {
    const {error} = $api.dmReply.delete(dmReplyId)
    if (error.value) replyErrorMessage.value = error.value.data.error
  }
}

const listenEventsOnChannel = () => {
  $echo.private(`dm-thread-${dmID.value}`)
      .listen('.dm-reply-sent', (data: any) => {
        dm.value.dm_thread.dm_replys.push(data.dmReply)
      })
      .listen('.dm-reply-updated', (data: any) => {
        dm.value.dm_thread.dm_replys = dm.value.dm_thread.dm_replys.map(reply => {
          if (reply.dm_reply_id == data.dmReply.dm_reply_id) return data.dmReply
          return reply
        })
      })
      .listen('.dm-reply-deleted', (data: any) => {
        dm.value.dm_thread.dm_replys = dm.value.dm_thread.dm_replys.filter(reply => {
          if (reply.dm_reply_id !== data.deletedDmReplyId) return reply
        })
      })
}

onMounted(() => {
  listenEventsOnChannel()
})

onBeforeUnmount(() => {
  $echo.leaveChannel(`dm-thread-${dmID.value}`)
})
</script>

<template>
  <section class="bg-gray-50 dark:bg-gray-900">
    <div class="flex flex-col items-center justify-center mx-auto">
      <div class="p-4 w-full max-w-2xl max-h-full mt-6">
        <div class="bg-white rounded-lg shadow dark:bg-gray-700">
          <div class="flex items-center ms-2.5">
            <NuxtLink to="/dm/list">
              <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0 4 4M1 5l4-4"/>
              </svg>
            </NuxtLink>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white p-4 md:p-5 space-y-4">
              DM Detail
            </h3>
          </div>

          <div class="p-4 md:p-5 space-y-4">
            <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow
                        dark:bg-gray-800 dark:border-gray-700 mb-4">
              <img class="w-10 h-10 rounded-full inline-block me-4" :src="storagePath + dm.owner.profile"
                   :alt="dm.owner.employee_name">
              <span class=" text-gray-500 dark:text-gray-400">{{ dm.owner.employee_name }}</span>
              <span class="ml-4 text-xs text-gray-500 dark:text-gray-400">
                {{ formatDistanceToNow(parseISO(dm.created_at), {addSuffix: true}) }}
              </span>
              <p class="mt-4 ">
                {{ dm.body }}
              </p>
            </div>
            <div
                v-if="dm.dm_thread.dm_replys.length > 0"
                v-for="reply in dm.dm_thread.dm_replys"
                :key="reply.dm_reply_id"
                class="block p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800
                              dark:border-gray-700 mb-4">
              <div class="flex items-center justify-between">
                <div>
                  <img class="w-10 h-10 rounded-full inline-block me-4" :src="storagePath + reply.created_user.profile"
                       alt="Jese image">
                  <span class=" text-gray-500 dark:text-gray-400">{{ reply.created_user.employee_name }}</span>
                  <span class="ml-4 text-xs text-gray-500 dark:text-gray-400">
                    {{ formatDistanceToNow(parseISO(reply.updated_at), {addSuffix: true}) }}
                  </span>
                  <span v-if="reply.created_at !== reply.updated_at" class="text-xs text-primary-700 ms-3">Edit</span>
                </div>
                <div v-if="user.id == reply.created_user.employee_id" class="flex items-center">
                  <svg
                      class="w-[15px] h-[15px] text-gray-800 dark:text-white hover:cursor-pointer"
                      aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 21 21"
                      @click="editDmReply(reply.body, reply.dm_reply_id)"
                  >
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7.418 17.861 1 20l2.139-6.418m4.279 4.279 10.7-10.7a3.027 3.027 0 0 0-2.14-5.165c-.802 0-1.571.319-2.139.886l-10.7 10.7m4.279 4.279-4.279-4.279m2.139 2.14 7.844-7.844m-1.426-2.853 4.279 4.279"/>
                  </svg>
                  <svg
                      class="w-[15px] h-[15px] text-gray-800 dark:text-white ms-3 hover:cursor-pointer"
                      aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 20"
                      @click="deleteDmReply(reply.dm_reply_id)"
                  >
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M1 5h16M7 8v8m4-8v8M7 1h4a1 1 0 0 1 1 1v3H6V2a1 1 0 0 1 1-1ZM3 5h12v13a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V5Z"/>
                  </svg>
                </div>
              </div>
              <p class="mt-4 ">
                {{ reply.body }}
              </p>
            </div>
            <form v-if="dm.replyable == REPLYABLE" @submit.prevent="handleSubmit">
              <div
                  class="w-full mb-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                <div class="px-4 py-2 bg-white rounded-t-lg dark:bg-gray-800">
                  <label for="comment" class="sr-only">Your comment</label>
                  <textarea id="comment" rows="4" v-model="dmReply" ref="replyTxtArea"
                            class="w-full px-0 text-sm text-gray-900 bg-white border-0 dark:bg-gray-800 focus:ring-0
                              dark:text-white dark:placeholder-gray-400"
                            placeholder="Write a comment..." required></textarea>
                </div>
                <div class="flex items-center justify-end px-3 py-2 border-t dark:border-gray-600">
                  <BtnWithSpinner :button="editBtn" :disabled="isLoading">
                    {{ isEditing ? "Edit reply" : "Post reply" }}
                  </BtnWithSpinner>
                  <Button v-if="isEditing" :button="cancelBtn" :disabled="false" @click="cancelEdit">
                    Cancel edit
                  </Button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>