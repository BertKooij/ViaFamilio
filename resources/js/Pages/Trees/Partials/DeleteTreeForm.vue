<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import ActionSection from '@/Components/ActionSection.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    tree: Object,
});

const confirmingTreeDeletion = ref(false);
const form = useForm();

const confirmTreeDeletion = () => {
    confirmingTreeDeletion.value = true;
};

const deleteTree = () => {
    form.delete(route('trees.destroy', props.tree), {
        errorBag: 'deleteTree',
    });
};
</script>

<template>
    <ActionSection>
        <template #title>
            Delete Tree
        </template>

        <template #description>
            Permanently delete this tree.
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
                Once a tree is deleted, all of its resources and data will be permanently deleted. Before deleting this tree, please download any data or information regarding this tree that you wish to retain.
            </div>

            <div class="mt-5">
                <DangerButton @click="confirmTreeDeletion">
                    Delete Tree
                </DangerButton>
            </div>

            <!-- Delete Tree Confirmation Modal -->
            <ConfirmationModal :show="confirmingTreeDeletion" @close="confirmingTreeDeletion = false">
                <template #title>
                    Delete Tree
                </template>

                <template #content>
                    Are you sure you want to delete this tree? Once a tree is deleted, all of its resources and data will be permanently deleted.
                </template>

                <template #footer>
                    <SecondaryButton @click="confirmingTreeDeletion = false">
                        Cancel
                    </SecondaryButton>

                    <DangerButton
                        class="ml-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteTree"
                    >
                        Delete Tree
                    </DangerButton>
                </template>
            </ConfirmationModal>
        </template>
    </ActionSection>
</template>
