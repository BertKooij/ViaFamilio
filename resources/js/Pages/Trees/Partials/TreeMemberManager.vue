<script setup>
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { useForm, usePage } from '@inertiajs/inertia-vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import ActionSection from '@/Components/ActionSection.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    tree: Object,
    availableRoles: Array,
    userPermissions: Object,
});

const addTreeMemberForm = useForm({
    email: '',
    role: null,
});

const updateRoleForm = useForm({
    role: null,
});

const leaveTreeForm = useForm();
const removeTreeMemberForm = useForm();

const currentlyManagingRole = ref(false);
const managingRoleFor = ref(null);
const confirmingLeavingTree = ref(false);
const treeMemberBeingRemoved = ref(null);

const addTreeMember = () => {
    addTreeMemberForm.post(route('tree-members.store', props.tree), {
        errorBag: 'addTreeMember',
        preserveScroll: true,
        onSuccess: () => addTreeMemberForm.reset(),
    });
};

const cancelTreeInvitation = (invitation) => {
    Inertia.delete(route('tree-invitations.destroy', invitation), {
        preserveScroll: true,
    });
};

const manageRole = (treeMember) => {
    managingRoleFor.value = treeMember;
    updateRoleForm.role = treeMember.membership.role;
    currentlyManagingRole.value = true;
};

const updateRole = () => {
    updateRoleForm.put(route('tree-members.update', [props.tree, managingRoleFor.value]), {
        preserveScroll: true,
        onSuccess: () => currentlyManagingRole.value = false,
    });
};

const confirmLeavingTree = () => {
    confirmingLeavingTree.value = true;
};

const leaveTree = () => {
    leaveTreeForm.delete(route('tree-members.destroy', [props.tree, usePage().props.value.user]));
};

const confirmTreeMemberRemoval = (treeMember) => {
    treeMemberBeingRemoved.value = treeMember;
};

const removeTreeMember = () => {
    removeTreeMemberForm.delete(route('tree-members.destroy', [props.tree, treeMemberBeingRemoved.value]), {
        errorBag: 'removeTreeMember',
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => treeMemberBeingRemoved.value = null,
    });
};

const displayableRole = (role) => {
    return props.availableRoles.find(r => r.key === role).name;
};
</script>

<template>
    <div>
        <div v-if="userPermissions.canAddTreeMembers">
            <SectionBorder />

            <!-- Add Tree Member -->
            <FormSection @submitted="addTreeMember">
                <template #title>
                    Add Tree Member
                </template>

                <template #description>
                    Add a new tree member to your tree, allowing them to collaborate with you.
                </template>

                <template #form>
                    <div class="col-span-6">
                        <div class="max-w-xl text-sm text-gray-600">
                            Please provide the email address of the person you would like to add to this tree.
                        </div>
                    </div>

                    <!-- Member Email -->
                    <div class="col-span-6 sm:col-span-4">
                        <InputLabel for="email" value="Email" />
                        <TextInput
                            id="email"
                            v-model="addTreeMemberForm.email"
                            type="email"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="addTreeMemberForm.errors.email" class="mt-2" />
                    </div>

                    <!-- Role -->
                    <div v-if="availableRoles.length > 0" class="col-span-6 lg:col-span-4">
                        <InputLabel for="roles" value="Role" />
                        <InputError :message="addTreeMemberForm.errors.role" class="mt-2" />

                        <div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer">
                            <button
                                v-for="(role, i) in availableRoles"
                                :key="role.key"
                                type="button"
                                class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"
                                :class="{'border-t border-gray-200 rounded-t-none': i > 0, 'rounded-b-none': i != Object.keys(availableRoles).length - 1}"
                                @click="addTreeMemberForm.role = role.key"
                            >
                                <div :class="{'opacity-50': addTreeMemberForm.role && addTreeMemberForm.role != role.key}">
                                    <!-- Role Name -->
                                    <div class="flex items-center">
                                        <div class="text-sm text-gray-600" :class="{'font-semibold': addTreeMemberForm.role == role.key}">
                                            {{ role.name }}
                                        </div>

                                        <svg v-if="addTreeMemberForm.role == role.key" class="ml-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>

                                    <!-- Role Description -->
                                    <div class="mt-2 text-xs text-gray-600 text-left">
                                        {{ role.description }}
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </template>

                <template #actions>
                    <ActionMessage :on="addTreeMemberForm.recentlySuccessful" class="mr-3">
                        Added.
                    </ActionMessage>

                    <PrimaryButton :class="{ 'opacity-25': addTreeMemberForm.processing }" :disabled="addTreeMemberForm.processing">
                        Add
                    </PrimaryButton>
                </template>
            </FormSection>
        </div>

        <div v-if="tree.tree_invitations.length > 0 && userPermissions.canAddTreeMembers">
            <SectionBorder />

            <!-- Tree Member Invitations -->
            <ActionSection class="mt-10 sm:mt-0">
                <template #title>
                    Pending Tree Invitations
                </template>

                <template #description>
                    These people have been invited to your tree and have been sent an invitation email. They may join the tree by accepting the email invitation.
                </template>

                <!-- Pending Tree Member Invitation List -->
                <template #content>
                    <div class="space-y-6">
                        <div v-for="invitation in tree.tree_invitations" :key="invitation.id" class="flex items-center justify-between">
                            <div class="text-gray-600">
                                {{ invitation.email }}
                            </div>

                            <div class="flex items-center">
                                <!-- Cancel Tree Invitation -->
                                <button
                                    v-if="userPermissions.canRemoveTreeMembers"
                                    class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none"
                                    @click="cancelTreeInvitation(invitation)"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </ActionSection>
        </div>

        <div v-if="tree.users.length > 0">
            <SectionBorder />

            <!-- Manage Tree Members -->
            <ActionSection class="mt-10 sm:mt-0">
                <template #title>
                    Tree Members
                </template>

                <template #description>
                    All of the people that are part of this tree.
                </template>

                <!-- Tree Member List -->
                <template #content>
                    <div class="space-y-6">
                        <div v-for="user in tree.users" :key="user.id" class="flex items-center justify-between">
                            <div class="flex items-center">
                                <img class="w-8 h-8 rounded-full" :src="user.profile_photo_url" :alt="user.name">
                                <div class="ml-4">
                                    {{ user.name }}
                                </div>
                            </div>

                            <div class="flex items-center">
                                <!-- Manage Tree Member Role -->
                                <button
                                    v-if="userPermissions.canAddTreeMembers && availableRoles.length"
                                    class="ml-2 text-sm text-gray-400 underline"
                                    @click="manageRole(user)"
                                >
                                    {{ displayableRole(user.membership.role) }}
                                </button>

                                <div v-else-if="availableRoles.length" class="ml-2 text-sm text-gray-400">
                                    {{ displayableRole(user.membership.role) }}
                                </div>

                                <!-- Leave Tree -->
                                <button
                                    v-if="$page.props.user.id === user.id"
                                    class="cursor-pointer ml-6 text-sm text-red-500"
                                    @click="confirmLeavingTree"
                                >
                                    Leave
                                </button>

                                <!-- Remove Tree Member -->
                                <button
                                    v-else-if="userPermissions.canRemoveTreeMembers"
                                    class="cursor-pointer ml-6 text-sm text-red-500"
                                    @click="confirmTreeMemberRemoval(user)"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </ActionSection>
        </div>

        <!-- Role Management Modal -->
        <DialogModal :show="currentlyManagingRole" @close="currentlyManagingRole = false">
            <template #title>
                Manage Role
            </template>

            <template #content>
                <div v-if="managingRoleFor">
                    <div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer">
                        <button
                            v-for="(role, i) in availableRoles"
                            :key="role.key"
                            type="button"
                            class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"
                            :class="{'border-t border-gray-200 rounded-t-none': i > 0, 'rounded-b-none': i !== Object.keys(availableRoles).length - 1}"
                            @click="updateRoleForm.role = role.key"
                        >
                            <div :class="{'opacity-50': updateRoleForm.role && updateRoleForm.role !== role.key}">
                                <!-- Role Name -->
                                <div class="flex items-center">
                                    <div class="text-sm text-gray-600" :class="{'font-semibold': updateRoleForm.role === role.key}">
                                        {{ role.name }}
                                    </div>

                                    <svg v-if="updateRoleForm.role == role.key" class="ml-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>

                                <!-- Role Description -->
                                <div class="mt-2 text-xs text-gray-600">
                                    {{ role.description }}
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="currentlyManagingRole = false">
                    Cancel
                </SecondaryButton>

                <PrimaryButton
                    class="ml-3"
                    :class="{ 'opacity-25': updateRoleForm.processing }"
                    :disabled="updateRoleForm.processing"
                    @click="updateRole"
                >
                    Save
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Leave Tree Confirmation Modal -->
        <ConfirmationModal :show="confirmingLeavingTree" @close="confirmingLeavingTree = false">
            <template #title>
                Leave Tree
            </template>

            <template #content>
                Are you sure you would like to leave this tree?
            </template>

            <template #footer>
                <SecondaryButton @click="confirmingLeavingTree = false">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    class="ml-3"
                    :class="{ 'opacity-25': leaveTreeForm.processing }"
                    :disabled="leaveTreeForm.processing"
                    @click="leaveTree"
                >
                    Leave
                </DangerButton>
            </template>
        </ConfirmationModal>

        <!-- Remove Tree Member Confirmation Modal -->
        <ConfirmationModal :show="treeMemberBeingRemoved" @close="treeMemberBeingRemoved = null">
            <template #title>
                Remove Tree Member
            </template>

            <template #content>
                Are you sure you would like to remove this person from the tree?
            </template>

            <template #footer>
                <SecondaryButton @click="treeMemberBeingRemoved = null">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    class="ml-3"
                    :class="{ 'opacity-25': removeTreeMemberForm.processing }"
                    :disabled="removeTreeMemberForm.processing"
                    @click="removeTreeMember"
                >
                    Remove
                </DangerButton>
            </template>
        </ConfirmationModal>
    </div>
</template>
