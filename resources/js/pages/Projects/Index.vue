<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Button, DataTable, Column, Dialog, InputText, Textarea, Tabs, TabList, Tab, TabPanels, TabPanel, FileUpload, MultiSelect, Checkbox, DatePicker } from 'primevue';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';

const toast = useToast();
const confirm = useConfirm();

const props = defineProps<{
    projects: Array<any>;
    services: Array<any>;
    skills: Array<any>;
    technologies: Array<any>;
    locales?: Record<string, string>;
}>();

const locales = props.locales || { en: 'English' };
const firstLocale = Object.keys(locales)[0];

const dialogVisible = ref(false);
const viewDialogVisible = ref(false);
const isEditing = ref(false);
const viewProjectData = ref<any>(null);
const activeLocale = ref(firstLocale);

const form = useForm({
    id: null as number | null,
    title: {} as Record<string, string>,
    slug: '',
    subtitle: {} as Record<string, string>,
    owner: '',
    short_description: {} as Record<string, string>,
    long_description: {} as Record<string, string>,
    hero_image: null as File | null,
    github_link: '',
    project_link: '',
    start_date: null as Date | null,
    project_date: null as Date | null,
    is_featured: false,
    is_published: true,
    services: [] as number[],
    skills: [] as number[],
    technologies: [] as number[],
});

const resetForm = () => {
    form.id = null;
    form.reset();
    form.title = {};
    form.subtitle = {};
    form.short_description = {};
    form.long_description = {};
    form.services = [];
    form.skills = [];
    form.technologies = [];
    form.is_featured = false;
    form.is_published = true;
    form.clearErrors();
};

const openCreateDialog = () => {
    isEditing.value = false;
    resetForm();
    activeLocale.value = firstLocale;
    dialogVisible.value = true;
};

const openEditDialog = (project: any) => {
    isEditing.value = true;
    form.id = project.id;
    form.title = project.title || {};
    form.slug = project.slug || '';
    form.subtitle = project.subtitle || {};
    form.owner = project.owner || '';
    form.short_description = project.short_description || {};
    form.long_description = project.long_description || {};
    form.github_link = project.github_link || '';
    form.project_link = project.project_link || '';
    form.start_date = project.start_date || null;
    form.project_date = project.project_date || null;
    form.is_featured = project.is_featured || false;
    form.is_published = project.is_published ?? true;
    form.services = project.services?.map((s: any) => s.id) || [];
    form.skills = project.skills?.map((s: any) => s.id) || [];
    form.technologies = project.technologies?.map((t: any) => t.id) || [];
    activeLocale.value = firstLocale;
    dialogVisible.value = true;
};

const onHeroImageSelect = (event: any) => { form.hero_image = event.files[0]; };

const saveProject = () => {
    confirm.require({
        message: isEditing.value ? 'Save changes to this project?' : 'Create this project?',
        header: 'Save Confirmation',
        icon: 'pi pi-save',
        acceptLabel: 'Yes, Save',
        rejectLabel: 'No, Cancel',
        acceptClass: 'p-button-success',
        accept: () => {

            // Format dates cleanly to YYYY-MM-DD for Laravel validation
            const startDate = form.start_date ? new Date(form.start_date).toISOString().split('T')[0] : null;
            const projectDate = form.project_date ? new Date(form.project_date).toISOString().split('T')[0] : null;

            const options = {
                preserveScroll: true,
                onSuccess: () => {
                    dialogVisible.value = false;
                    toast.add({ severity: 'success', summary: 'Success', detail: `Project ${isEditing.value ? 'updated' : 'created'} successfully.`, life: 3000 });
                },
                onError: (errors: any) => {
                    const errorMessages = Object.values(errors || {}).flat().join(' ');
                    toast.add({ severity: 'error', summary: 'Validation Failed', detail: errorMessages || 'Check the form.', life: 5000 });
                }
            };

            if (isEditing.value && form.id) {
                // Editing: Apply PUT spoofing inside the transform
                form.transform((data) => ({
                    ...data,
                    start_date: startDate,
                    project_date: projectDate,
                    _method: 'PUT', // Only applied here
                })).post(route('projects.update', form.id), options);
            } else {
                // Creating: No PUT spoofing applied
                form.transform((data) => ({
                    ...data,
                    start_date: startDate,
                    project_date: projectDate,
                })).post(route('projects.store'), options);
            }
        }
    });
};

const deleteProject = (project: any) => {
    confirm.require({
        message: `Delete project "${project.title?.en}"?`,
        header: 'Delete Confirmation',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('projects.destroy', project.id), {
                preserveScroll: true,
                onSuccess: () => { toast.add({ severity: 'success', summary: 'Deleted', detail: 'Project deleted.', life: 3000 }); }
            });
        }
    });
};
</script>

<template>

    <Head title="Projects" />

    <DashboardLayout>
        <div
            class="bg-white dark:bg-graphite-950 p-6 rounded-xl shadow-soft border border-graphite-200 dark:border-graphite-800">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-xl font-semibold text-graphite-800 dark:text-white">Projects Management</h1>
                <Button label="Add New Project" icon="pi pi-plus" @click="openCreateDialog" />
            </div>

            <DataTable :value="props.projects" tableStyle="min-width: 50rem">
                <Column field="id" header="ID" style="width: 5%"></Column>
                <Column header="Image" style="width: 10%">
                    <template #body="slotProps">
                        <img v-if="slotProps.data.hero_image" :src="slotProps.data.hero_image"
                            class="w-12 h-12 object-cover rounded-lg" />
                        <span v-else class="text-graphite-400 text-xs">No Image</span>
                    </template>
                </Column>
                <Column header="Title">
                    <template #body="slotProps">
                        <div class="flex flex-col">
                            <span class="font-medium text-graphite-800 dark:text-white">{{ slotProps.data.title?.en ||
                                'N/A' }}</span>
                            <span class="text-xs text-graphite-500">{{ slotProps.data.subtitle?.en }}</span>
                        </div>
                    </template>
                </Column>
                <Column header="Status" style="width: 10%">
                    <template #body="slotProps">
                        <span
                            :class="['px-2 py-1 text-xs rounded-full', slotProps.data.is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500']">
                            {{ slotProps.data.is_published ? 'Published' : 'Draft' }}
                        </span>
                    </template>
                </Column>
                <Column header="Actions" style="width: 15%">
                    <template #body="slotProps">
                        <div class="flex gap-2">
                            <!-- New View Button -->
                            <Button icon="pi pi-eye" severity="secondary" text rounded
                                @click="router.get(route('projects.show', slotProps.data.id))" />
                            <Button icon="pi pi-pencil" severity="info" text rounded
                                @click="openEditDialog(slotProps.data)" />
                            <Button icon="pi pi-trash" severity="danger" text rounded
                                @click="deleteProject(slotProps.data)" />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>

        <!-- Create/Edit Dialog -->
        <Dialog v-model:visible="dialogVisible" :header="isEditing ? 'Edit Project' : 'Add New Project'" modal
            class="p-fluid w-[60rem]">
            <div class="space-y-6 pt-4 max-h-[70vh] overflow-y-auto pr-2">

                <!-- Translations -->
                <Tabs :value="activeLocale">
                    <TabList>
                        <Tab v-for="(label, code) in locales" :key="code" :value="code">{{ label }}</Tab>
                    </TabList>
                    <TabPanels>
                        <TabPanel v-for="(label, code) in locales" :key="code" :value="code">
                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Title
                                        ({{ label }}) *</label>
                                    <InputText v-model="form.title[code]" class="w-full" />
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Subtitle
                                        ({{ label }})</label>
                                    <InputText v-model="form.subtitle[code]" class="w-full" />
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Short
                                        Description ({{ label }})</label>
                                    <Textarea v-model="form.short_description[code]" rows="2" class="w-full" />
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Long
                                        Description ({{ label }})</label>
                                    <Textarea v-model="form.long_description[code]" rows="4" class="w-full" />
                                </div>
                            </div>
                        </TabPanel>
                    </TabPanels>
                </Tabs>

                <!-- Meta Data -->
                <div
                    class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-graphite-200 dark:border-graphite-700">
                    <div>
                        <label
                            class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Slug</label>
                        <InputText v-model="form.slug" class="w-full" placeholder="auto-generated if empty" />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Owner</label>
                        <InputText v-model="form.owner" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">GitHub
                            Link</label>
                        <InputText v-model="form.github_link" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Project
                            Link</label>
                        <InputText v-model="form.project_link" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Start
                            Date</label>
                        <DatePicker v-model="form.start_date" dateFormat="yy-mm-dd" class="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Project
                            Date</label>
                        <DatePicker v-model="form.project_date" dateFormat="yy-mm-dd" class="w-full" />
                    </div>
                </div>

                <!-- Relations -->
                <div
                    class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-graphite-200 dark:border-graphite-700">
                    <div>
                        <label
                            class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Services</label>
                        <MultiSelect v-model="form.services" :options="props.services" optionLabel="name.en"
                            optionValue="id" placeholder="Select Services" class="w-full" />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Skills</label>
                        <MultiSelect v-model="form.skills" :options="props.skills" optionLabel="title.en"
                            optionValue="id" placeholder="Select Skills" class="w-full" />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Technologies</label>
                        <MultiSelect v-model="form.technologies" :options="props.technologies" optionLabel="name.en"
                            optionValue="id" placeholder="Select Technologies" class="w-full" />
                    </div>
                </div>

                <!-- Media & Flags -->
                <div
                    class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-graphite-200 dark:border-graphite-700">
                    <div>
                        <label class="block text-sm font-medium text-graphite-600 dark:text-graphite-400 mb-2">Hero
                            Image</label>
                        <FileUpload mode="basic" chooseLabel="Upload Hero" accept="image/*"
                            class="p-button-outlined w-full" @select="onHeroImageSelect" />
                    </div>
                    <div class="flex items-center gap-8 pt-6">
                        <div class="flex items-center gap-2">
                            <Checkbox v-model="form.is_featured" :binary="true" inputId="featured" />
                            <label for="featured"
                                class="text-sm text-graphite-600 dark:text-graphite-300">Featured</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <Checkbox v-model="form.is_published" :binary="true" inputId="published" />
                            <label for="published"
                                class="text-sm text-graphite-600 dark:text-graphite-300">Published</label>
                        </div>
                    </div>
                </div>

            </div>

            <template #footer>
                <Button label="Cancel" icon="pi pi-times" @click="dialogVisible = false" text />
                <Button label="Save" icon="pi pi-check" @click="saveProject" :loading="form.processing" />
            </template>
        </Dialog>
    </DashboardLayout>
</template>