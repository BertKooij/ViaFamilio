<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    tree: Object,
    permissions: Object,
});

const form = useForm({
    name: props.tree.name,
});

const updateTreeName = () => {
    form.put(route('trees.update', props.tree), {
        errorBag: 'updateTreeName',
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection @submitted="updateTreeName">
        <template #title>
            Tree Name
        </template>

        <template #description>
            The tree's name and owner information.
        </template>

        <template #form>
            <!-- Tree Owner Information -->
            <div class="col-span-6">
                <InputLabel value="Tree Owner" />

                <div class="flex items-center mt-2">
                    <img class="w-12 h-12 rounded-full object-cover" :src="tree.owner.profile_photo_url" :alt="tree.owner.name">

                    <div class="ml-4 leading-tight">
                        <div>{{ tree.owner.name }}</div>
                        <div class="text-gray-700 text-sm">
                            {{ tree.owner.email }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tree Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="name" value="Tree Name" />

                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    :disabled="! permissions.canUpdateTree"
                />

                <InputError :message="form.errors.name" class="mt-2" />
            </div>
        </template>

        <template v-if="permissions.canUpdateTree" #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>
