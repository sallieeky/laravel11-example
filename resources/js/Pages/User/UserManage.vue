<template>
    <Head title="User Management" />
    <MainLayout title="User Management">
        <template #header-action>
            <BsButton type="primary" icon="plus" @click="addUserAction" v-if="can('user.create')">Add User</BsButton>
            <BsButton type="primary" icon="arrows-up-down" @click="syncLeader" v-if="btnSyncLeaderVisible && can('user.update')">Sync Leader</BsButton>
            <BsButton type="primary" icon="arrows-up-down" @click="syncPlt" v-if="btnSyncLeaderVisible && can('user.update')">Sync Plt</BsButton>
        </template>
        <div class="flex flex-col">
            <DxDataGrid ref="datagridRef" :data-source="dataSource" key="user_id" @selection-changed="onSelectionChanged" @cell-dbl-click="editUserAction($event.data)">
                <!-- Your column here -->
                <DxColumn data-field="profile_picture" caption="Profile" :allowHeaderFiltering="false" width="100" alignment="center" cell-template="profile" :allowExporting="false" />
                <template #profile="{ data }">
                    <el-image
                        :src="route('account-picture', {npk: data.data?.npk || 'default'})"
                        fit="cover"
                        class="rounded-full"
                        :preview-teleported="true"
                        style="width: 50px; height: 50px;"
                        :preview-src-list="[route('account-picture', {npk: (data.data?.npk || 'default')})]"
                    />
                </template>
                <DxColumn data-field="username" caption="Username" :allowHeaderFiltering="false" />
                <DxColumn data-field="npk" caption="NPK" :allowHeaderFiltering="false" />
                <DxColumn data-field="name" caption="Nama" :allowHeaderFiltering="false" />
                <DxColumn data-field="email" caption="Email" :allowHeaderFiltering="false" />
                <DxColumn caption="Role" cell-template="role" width="200" :allowExporting="false" />
                <template #role="{ data }">
                    <div class="flex flex-row justify-start items-center" v-if="data.data.roles.length > 0">
                        <div class="bg-primary rounded-full px-3 py-1 text-white m-px w-min text-xs">
                            {{ data.data.roles[0].name }}
                        </div>

                        <el-popover placement="top" :width="150" trigger="hover" v-if="data.data.roles.length > 1">
                            <template #reference>
                                <div
                                    class=" bg-primary-hover rounded-full px-2 py-1 text-white m-px w-min text-xs cursor-pointer">
                                    + {{ data.data.roles.length - 1 }}
                                </div>
                            </template>
                            <template #default>
                                <div class="w-full flex flex-col justify-center items-center">
                                    <div class="bg-primary rounded-full px-3 py-1 text-white m-px text-xs w-fit"
                                        v-for="role in data.data.roles.slice(1, data.data.roles.length)">
                                        {{ role.name }}
                                    </div>
                                </div>
                            </template>
                        </el-popover>
                    </div>
                </template>
                <DxColumn data-field="is_active" caption="Status" cell-template="user-status" width="110" alignment="center" :allowFiltering="true" :allowHeaderFiltering="false" data-type="boolean" false-text="Inactive" true-text="Active" :filter-values="[0, 1]"/>
                <template #user-status="{ data }">
                    <span v-if="data.data.is_active"
                        class="px-4 py-2 rounded-md bg-success text-white text-xs">Active</span>
                    <span v-else class="px-4 py-2 rounded-md bg-danger text-white text-xs">Inactive</span>
                </template>

                <DxColumn cell-template="action" width="60" alignment="center" :allowExporting="false" :showInColumnChooser="false" :fixed="true" fixed-position="right" v-if="can('user.update|user.delete')"/>
                <template #action="{ data }">
                    <el-dropdown trigger="click" placement="bottom-end">
                        <span class="el-dropdown-link">
                            <BsIcon icon="ellipsis-vertical" />
                        </span>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item v-if="can('user.update')" @click="editUserAction(data.data)" >
                                    <BsIcon icon="pencil-square" class="mr-2" /> Edit User
                                </el-dropdown-item>
                                <el-dropdown-item @click="changePasswordAction(data.data)" v-if="can('user.update')">
                                    <BsIcon icon="key" class="mr-2" /> Change Local Password
                                </el-dropdown-item>
                                <el-dropdown-item v-if="!data.data.is_active && can('user.update')" @click="switchUserStatus(data.data, true)">
                                    <BsIcon icon="arrow-path-rounded-square" class="mr-2" /> Enable User
                                </el-dropdown-item>
                                <el-dropdown-item v-else-if="can('user.update')" @click="switchUserStatus(data.data, false)">
                                    <BsIcon icon="arrow-path-rounded-square" class="mr-2" /> Disable User
                                </el-dropdown-item>
                                <el-dropdown-item v-if="can('user.delete')" @click="deleteUserAction(data.data)">
                                    <BsIcon icon="trash" class="mr-2" /> Delete User
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </template>
                <!-- End your column here -->

                <!-- Custom toolbar -->
                <DxToolbar>
                    <DxItem location="before" template="buttonTemplate" />
                    <DxItem name="columnChooserButton" />
                    <DxItem name="exportButton" />
                    <DxItem widget="dxButton" :options="{ icon: 'refresh', onClick: refreshDatagrid }" />
                </DxToolbar>
                <template #buttonTemplate>
                    <div class="flex w-full">
                        <Transition name="fadetransition" mode="out-in" appear>
                            <div v-if="!itemSelected">
                                <!-- Table Header Action Here -->
                            </div>
                            <div v-else class="flex items-center border-2 border-primary-border rounded-full gap-1 text-sm">
                                <BsIconButton icon="x-mark" @click="clearSelection" />
                                <span class="font-bold mr-2">{{ dataSelected.length }} Selected</span>

                                <div class="flex items-center border-l-2 px-2 h-full gap-1">
                                    <div class="flex items-center rounded-full hover:bg-gray-200 cursor-pointer" @click="switchUserStatus(dataSelected, true)"  v-if="can('user.update')">
                                        <BsIconButton icon="check-circle" class="text-success" />
                                        <span class="mr-2 font-semibold">Enable</span>
                                    </div>
                                    <div class="flex items-center rounded-full hover:bg-gray-200 cursor-pointer" @click="switchUserStatus(dataSelected, false)"  v-if="can('user.update')">
                                        <BsIconButton icon="x-circle" class="text-danger" />
                                        <span class="mr-2 font-semibold">Disable</span>
                                    </div>
                                    <p class="font-semibold italic text-gray-700" v-if="!can('user.update')">No Action</p>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </template>
                <!-- End Custom toolbar -->
            </DxDataGrid>
        </div>
        <el-dialog v-model="dialogFormVisible" width="500px" :append-to-body="true" :destroy-on-close="true"
            class="!rounded-xl">
            <template #header>
                <span class="font-bold text-lg">{{ !editMode ? 'Create' : 'Edit' }} User</span>
            </template>
            <el-form ref="formUserRef" :model="formUser" label-width="200px" label-position="top"
                require-asterisk-position="right" autocomplete="off">
                <el-form-item :error="getFormError('username')" prop="username" label="Username" :required="true">
                    <el-input v-model="formUser.username" autocomplete="one-time-code" autocorrect="off"
                        spellcheck="false" />
                </el-form-item>
                <el-form-item :error="getFormError('name')" prop="name" label="Nama" :required="true">
                    <el-input v-model="formUser.name" autocomplete="one-time-code" autocorrect="off"
                        spellcheck="false" />
                </el-form-item>
                <el-form-item :error="getFormError('npk')" prop="npk" label="NPK">
                    <el-input v-model="formUser.npk" autocomplete="one-time-code" autocorrect="off"
                        spellcheck="false" />
                </el-form-item>
                <el-form-item :error="getFormError('email')" prop="email" label="Email">
                    <el-input type="email" v-model="formUser.email" autocomplete="one-time-code" autocorrect="off"
                        spellcheck="false" />
                </el-form-item>
                <el-form-item :error="getFormError('password')" prop="password" label="Password" :required="true" v-if="!editMode">
                    <el-input type="password" v-model="formUser.password" autocomplete="one-time-code" autocorrect="off"
                        spellcheck="false" />
                </el-form-item>
                <el-form-item :error="getFormError('role')" props="role" label="Role">
                    <el-select v-model="formUser.role" multiple placeholder="Select" class="w-full">
                        <el-option v-for="role in roles" :key="role.id" :label="role.name" :value="role.id" />
                    </el-select>
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer flex">
                    <BsButton class="flex-grow" type="primary-outline" @click="closeDialog">Cancel</BsButton>
                    <BsButton class="flex-grow" v-if="!editMode" type="primary" @click="addUserSubmitAction">Submit</BsButton>
                    <BsButton class="flex-grow" v-if="editMode" type="primary" @click="editUserSubmitAction">Update</BsButton>
                </span>
            </template> 
        </el-dialog>

        <el-dialog v-model="dialogChangePasswordVisible" width="500px" :append-to-body="true" :destroy-on-close="true" class="!rounded-xl">
            <template #header>
                <span class="font-bold text-lg">Change User Password</span>
            </template>
            <el-form ref="formPasswordRef" :model="formPassword" label-width="200px" label-position="top" require-asterisk-position="right" autocomplete="off">
                <el-form-item label="Username">
                    <el-input v-model="formPassword.username" disabled/>
                </el-form-item>
                <el-form-item label="Name">
                    <el-input v-model="formPassword.name" disabled/>
                </el-form-item>
                <el-form-item :error="getFormPasswordError('new_password')" prop="new_password" label="New Password" :required="true" :rules=" { required: true, message: 'Field must not be empty', trigger: 'blur' }">
                    <el-input type="password" v-model="formPassword.new_password" autocomplete="new-password" spellcheck="false" />
                </el-form-item>
                <el-form-item :error="getFormPasswordError('confirm_password')" prop="confirm_password" label="Confirm Password" :required="true" :rules=" { required: true, message: 'Field must not be empty', trigger: 'blur' }">
                    <el-input type="password" v-model="formPassword.confirm_password" autocomplete="new-password" spellcheck="false" />
                </el-form-item>
            </el-form>
            <div class="ml-4">
                <div class="flex flex-row gap-2 text-xs items-center" :class="[{'text-primary':passwordCondtion[0]},{'text-gray-600':!passwordCondtion[0]}]">
                    <BsIcon icon="check-circle"/>
                    <span>8-50 Character</span>
                </div>
                <div class="flex flex-row gap-2 text-xs items-center" :class="[{'text-primary':passwordCondtion[1]},{'text-gray-600':!passwordCondtion[1]}]">
                    <BsIcon icon="check-circle"/>
                    <span>A lowercase character</span>
                </div>
                <div class="flex flex-row gap-2 text-xs items-center" :class="[{'text-primary':passwordCondtion[2]},{'text-gray-600':!passwordCondtion[2]}]">
                    <BsIcon icon="check-circle"/>
                    <span>An uppercase character</span>
                </div>
                <div class="flex flex-row gap-2 text-xs items-center" :class="[{'text-primary':passwordCondtion[3]},{'text-gray-600':!passwordCondtion[3]}]">
                    <BsIcon icon="check-circle"/>
                    <span>A number</span>
                </div>
                <div class="flex flex-row gap-2 text-xs items-center" :class="[{'text-primary':passwordCondtion[4]},{'text-gray-600':!passwordCondtion[4]}]">
                    <BsIcon icon="check-circle"/>
                    <span>A special character / symbol</span>
                </div>
                <div class="flex flex-row gap-2 text-xs items-center" :class="[{'text-primary':passwordCondtion[5]},{'text-gray-600':!passwordCondtion[5]}]">
                    <BsIcon icon="check-circle"/>
                    <span>Password Match</span>
                </div>
            </div>
            <template #footer>
                <span class="dialog-footer flex">
                    <BsButton class="flex-grow" type="primary-outline" @click="closePasswordDialog">Cancel</BsButton>
                    <BsButton class="flex-grow" type="primary" @click="changePasswordSubmitAction">Submit</BsButton>
                </span>
            </template>
        </el-dialog>
    </MainLayout>
</template>
<script setup>
import { reactive, ref } from 'vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { can } from '@/Core/Helpers/permission-check';
import {
    DxColumn,
    DxDataGrid,
    DxItem,
    DxToolbar
} from 'devextreme-vue/data-grid';
import CustomStore from "devextreme/data/custom_store";
import { computed } from 'vue';
import BsButton from '@/Components/BsButton.vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import BsIcon from '@/Components/BsIcon.vue';
import BsIconButton from '@/Components/BsIconButton.vue';
import { ElLoading } from 'element-plus';
import { dxLoad } from '@/Core/Helpers/dx-helpers';

// DIALOG FORM
const formUserRef = ref();
const dialogFormVisible = ref(false);
const editMode = ref(false);
const roles = computed(() => usePage().props.roles);
const btnSyncLeaderVisible = computed(()=>usePage().props.leader_enabled);

const formUser = useForm({
    user_id: '',
    user_uuid: '',
    username: '',
    name: '',
    npk: '',
    email: '',
    password: '',
    role: [],
});
const formUserErrors = ref([]);

function getFormError(field, errors = formUserErrors.value) {
    if (!errors && !errors.length) {
        return false
    }
    if (errors[field]) {
        return errors[field]
    }
}
function closeDialog() {
    dialogFormVisible.value = false;
}
function addUserAction() {
    editMode.value = false;
    dialogFormVisible.value = true;

    formUser.user_uuid = '';
    formUser.user_id = '';
    formUser.username = '';
    formUser.name = '';
    formUser.npk = '';
    formUser.email = '';
    formUser.password = '';
    formUser.role = [];
}
async function addUserSubmitAction() {
    await formUserRef.value.validate((valid, _) => {
        if (valid) {
            formUser.post(route('user.create'), {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    refreshDatagrid();
                    formUserErrors.value = [];
                    dialogFormVisible.value = false;
                },
                onError: (errors) => {
                    formUserErrors.value = errors;
                    if('message' in errors){
                        ElMessage({
                            message: errors.message,
                            type: 'error',
                        });
                    }
                }
            });
        }
    });
}
function editUserAction(dataUser) {
    editMode.value = true;
    dialogFormVisible.value = true;

    formUser.user_id = dataUser.user_id;
    formUser.user_uuid = dataUser.user_uuid;
    formUser.username = dataUser.username;
    formUser.name = dataUser.name;
    formUser.npk = dataUser.npk;
    formUser.email = dataUser.email;
    formUser.role = dataUser.roles.map(role => role.id);
}
async function editUserSubmitAction() {
    await formUserRef.value.validate(async (valid, _) => {
        if (valid) {
            formUser.put(route('user.update',formUser.user_uuid), {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    refreshDatagrid();
                    formUserErrors.value = [];
                    dialogFormVisible.value = false;
                },
                onError: (errors) => {
                    formUserErrors.value = errors;
                    if('message' in errors){
                        ElMessage({
                            message: errors.message,
                            type: 'error',
                        });
                    }
                }
            });
        }
    });
}
function deleteUserAction(dataUser) {
    ElMessageBox.confirm(
        'Are you sure to delete this user ?',
        'Warning',
        {
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            type: 'warning',
        }
    )
        .then(() => {
            router.delete(route('user.delete',dataUser.user_uuid), {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    refreshDatagrid();
                    dialogFormVisible.value = false;
                },
                onError: (errors) => {
                    formUserErrors.value = errors;
                }
            });
        })
        .catch(() => {
            ElMessage({
                type: 'info',
                message: 'Action Canceled',
            })
        })
}
function switchUserStatus(dataUser, status) {
    if (Array.isArray(dataUser)) {
        ElMessageBox.confirm(
            'Are you sure to switch these users status to ' + (status ? 'Active' : 'Inactive') + ' ?',
            'Warning',
            {
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel',
                type: 'warning',
            }
        ).then(() => {
            dataUser.forEach((user) => {
                switchUserStatus(user, status);
            });
        }).catch(() => {
            ElMessage({
                type: 'info',
                message: 'Action Canceled',
            })
        });
    } else {
        useForm({
            is_active : status
        }).put(route('user.switch_status', dataUser.user_uuid), {
            onSuccess: (response) => {
                ElMessage({
                    message: response.props.flash.message,
                    type: 'success',
                });
                refreshDatagrid();
                dialogFormVisible.value = false;
            },
            onError: (errors) => {
                formUserErrors.value = errors;
            },
        });
    }
}
function syncLeader(){
    const loading = ElLoading.service({
        lock: true,
        text: "Sync PKT Leader ...",
    });
    formUser.post(route('user.sync_leader'), {
        onSuccess: (response) => {
            ElMessage({
                message: response.props.flash.message,
                type: 'success',
            });
            refreshDatagrid();
            loading.close();

        },
        onError: (errors) => {
            if('message' in errors){
                ElMessage({
                    message: errors.message,
                    type: 'error',
                });
            }
            loading.close();
        }
    });
}
function syncPlt(){
    const loading = ElLoading.service({
        lock: true,
        text: "Sync PLT from leader ...",
    });
    formUser.post(route('user.sync_plt'), {
        onSuccess: (response) => {
            ElMessage({
                message: response.props.flash.message,
                type: 'success',
            });
            refreshDatagrid();
            loading.close();

        },
        onError: (errors) => {
            if('message' in errors){
                ElMessage({
                    message: errors.message,
                    type: 'error',
                });
            }
            loading.close();
        }
    });
}

// DIALOG CHANGE PASSWORD
const dialogChangePasswordVisible = ref(false);

const formPasswordRef = ref();
const formPassword = reactive({
    user_id: '',
    user_uuid: '',
    username: '',
    name: '',
    new_password: '',
    confirm_password: ''
});

const formPasswordErrors = ref([]);

var passwordCondtion = computed(()=>{
    return [
        formPassword.new_password.length >= 8 && formPassword.new_password.length <= 50,    
        /[a-z]/.test(formPassword.new_password),                                            
        /[A-Z]/.test(formPassword.new_password),                                            
        /[0-9]/.test(formPassword.new_password),                                            
        /[^a-zA-Z0-9]/.test(formPassword.new_password),                                     
        formPassword.new_password === formPassword.confirm_password && formPassword.new_password != '',
    ];
})

function getFormPasswordError(field, errors = formPasswordErrors.value) {
    if (!errors && !errors.length) {
        return false;
    }
    if (errors[field]) {
        return errors[field];
    }
}

function closePasswordDialog() {
    dialogChangePasswordVisible.value = false;
}

function changePasswordAction(dataUser) {
    formPassword.user_id = dataUser.user_id;
    formPassword.user_uuid = dataUser.user_uuid;
    formPassword.name = dataUser.name;
    formPassword.username = dataUser.username;
    dialogChangePasswordVisible.value = true;
    formPassword.new_password = '';
    formPassword.confirm_password = '';
}

async function changePasswordSubmitAction() {
    var formPasswordErrorsPointer = formPasswordErrors;
    formPasswordErrorsPointer.value = [];
    await formPasswordRef.value.validate((valid) => {
        if (valid) {
            if (formPassword.new_password !== formPassword.confirm_password) {
                formPasswordErrorsPointer.value = { confirm_password: 'Passwords do not match' };
                return;
            }
            if (!passwordCondtion.value.every(element => element === true)){
                formPasswordErrorsPointer.value = { new_password: 'The password does not meet the criteria.' };
                return;
            }

            const formSubmit = useForm({
                user_id: formPassword.user_id,
                new_password: formPassword.new_password
            });

            formSubmit.post(route('user.change_password',formPassword.user_uuid), {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    dialogChangePasswordVisible.value = false;
                },
                onError: (errors) => {
                    if('message' in errors){
                        ElMessage({
                            message: errors.message,
                            type: 'error',
                        });
                    }
                }
            });
        }
    });
}

// DEVEXTREME DATAGRID
const datagridRef = ref();
const dataSelected = ref([]);
var itemSelected = computed(() => dataSelected.value.length > 0);

// ========================================================================
// Data source using server side processing
// ========================================================================
const dataKey = 'user_id'; //change to data primary key
const dataRoute = route('user.data_processing') //change to data processing route
const dataSource = new CustomStore({
    key: dataKey,
    load: dxLoad(dataRoute).bind(this),
});

// On Refresh Datagrid
function refreshDatagrid() {
    datagridRef.value.instance.refresh();
};

// On Selection Changed
function onSelectionChanged(data) {
    dataSelected.value = data.selectedRowsData;
};

// Clear Selection
function clearSelection() {
    const dataGrid = datagridRef.value.instance;
    dataGrid.clearSelection();
    dataSelected.value = [];
}

</script>