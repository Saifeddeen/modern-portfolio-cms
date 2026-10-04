<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Button, DataTable, Column, Dialog, useToast, useConfirm } from 'primevue';

const toast = useToast();
const confirm = useConfirm();

const props = defineProps<{ messages: Array<any> }>();

const viewDialog = ref(false);
const activeMessage = ref<any>(null);

const openMessage = (msg: any) => {
    activeMessage.value = msg;
    viewDialog.value = true;

    if (!msg.is_read) {
        router.put(route('messages.markAsRead', msg.id), {}, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                msg.is_read = true;
                // Added the Toast notification here
                toast.add({
                    severity: 'success',
                    summary: 'Marked as Read',
                    detail: 'Message has been marked as read.',
                    life: 3000
                });
            }
        });
    }
};

const deleteMessage = (msg: any) => {
    confirm.require({
        message: 'Are you sure you want to delete this message?',
        header: 'Delete Confirmation',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('messages.destroy', msg.id), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.add({ severity: 'success', summary: 'Deleted', detail: 'Message deleted.', life: 3000 });
                    viewDialog.value = false;
                }
            });
        }
    });
};

// --- REAL-TIME TABLE UPDATE LOGIC ---
onMounted(() => {
    if (window.Echo) {
        window.Echo.private('dashboard')
            .listen('MessageReceived', (e: any) => {
                router.reload();
            });
    }
});

onUnmounted(() => {
    if (window.Echo) {
        window.Echo.private('dashboard').stopListening('MessageReceived');
    }
});
</script>

<template>

    <Head title="Messages" />
    <DashboardLayout>
        <div
            class="bg-white dark:bg-graphite-950 p-6 rounded-xl shadow-soft border border-graphite-200 dark:border-graphite-800">
            <h1 class="text-xl font-semibold text-graphite-800 dark:text-white mb-6">Inbox</h1>

            <DataTable :value="props.messages" tableStyle="min-width: 50rem">
                <Column header="Status" style="width: 10%">
                    <template #body="slotProps">
                        <span v-if="!slotProps.data.is_read"
                            class="bg-iris-500 text-white text-xs px-2 py-1 rounded-full">New</span>
                        <span v-else class="text-graphite-400 text-xs">Read</span>
                    </template>
                </Column>
                <Column field="name" header="Name" style="width: 20%"></Column>
                <Column field="subject" header="Subject" style="width: 30%">
                    <template #body="slotProps">
                        <button @click="openMessage(slotProps.data)"
                            class="text-left font-medium text-iris-500 hover:underline">
                            {{ slotProps.data.subject }}
                        </button>
                    </template>
                </Column>
                <Column field="created_at" header="Date" style="width: 20%">
                    <template #body="slotProps">
                        <span class="text-sm text-graphite-500">{{ new Date(slotProps.data.created_at).toLocaleString()
                        }}</span>
                    </template>
                </Column>
                <Column header="Actions" style="width: 10%">
                    <template #body="slotProps">
                        <Button icon="pi pi-trash" severity="danger" text rounded
                            @click="deleteMessage(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>
        </div>

        <Dialog v-model:visible="viewDialog" header="Message Details" modal class="p-fluid w-[40rem]">
            <div v-if="activeMessage" class="space-y-4 pt-4">
                <div class="grid grid-cols-2 gap-4 pb-4 border-b border-graphite-200 dark:border-graphite-700">
                    <div>
                        <p class="text-xs text-graphite-500">From</p>
                        <p class="font-medium text-graphite-800 dark:text-white">{{ activeMessage.name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-graphite-500">Email</p>
                        <p class="font-medium text-iris-500">{{ activeMessage.email }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-graphite-500">Subject</p>
                    <p class="font-medium text-graphite-800 dark:text-white">{{ activeMessage.subject }}</p>
                </div>
                <div>
                    <p class="text-xs text-graphite-500">Message</p>
                    <p class="text-sm text-graphite-700 dark:text-graphite-300 whitespace-pre-line">{{
                        activeMessage.message }}
                    </p>
                </div>
            </div>
        </Dialog>
    </DashboardLayout>
</template>