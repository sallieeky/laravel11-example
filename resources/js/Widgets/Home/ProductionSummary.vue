<template>
    <div class="p-4 rounded-xl border border-gray-400 flex-1">
    <div class="flex flex-row justify-between mb-4">
        <div>
            <div class="font-bold">Production Summary</div>
            <div class="font-thin text-gray-700 text-sm">Summary of production data</div>
        </div>
    </div>
    <div>
        <DxDataGrid ref="datagridRef" :data-source="dataSource" key="product_id" @selection-changed="onSelectionChanged">
            <!-- Your column here -->
            <DxColumn data-field="product" caption="Product" />
            <DxColumn data-field="quantity" caption="Quantity" :dataType="'number'">
                <DxFormat type="fixedPoint" :precision="0" />
            </DxColumn>
            <DxColumn data-field="target" caption="Target" :dataType="'number'">
                <DxFormat type="fixedPoint" :precision="0" />
            </DxColumn>
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
                            <!-- Table Action Here -->
                        </div>
                        <div v-else class="flex items-center border-2 border-primary-border rounded-full gap-1 text-sm">
                            <BsIconButton icon="x-mark" @click="clearSelection" />
                            <span class="font-bold mr-2">{{ dataSelected.length }} Selected</span>

                            <div class="flex items-center border-l-2 px-2 h-full">
                                <!-- Table Bulk Action -->
                                <p class="font-semibold italic text-gray-700">No Action</p>
                                <!-- End Table Bulk Action -->
                            </div>
                        </div>
                    </Transition>
                </div>
            </template>
            <!-- End Custom toolbar -->
        </DxDataGrid>
    </div>
</div>
</template>

<script setup>
import { defineProps, ref, computed } from 'vue';
import BsIconButton from '@/Components/BsIconButton.vue';
import {
    DxColumn,
    DxDataGrid,
    DxFormat,
    DxItem,
    DxToolbar
} from 'devextreme-vue/data-grid';
import CustomStore from "devextreme/data/custom_store";
import { dxLoad } from '@/Core/Helpers/dx-helpers';

const props = defineProps({
    dataSource: {
        type: Array,
        // Only for dummy data, you can remove this
        default: () => {
            let data = [];
            for (let i = 0; i < 50; i++) {
                data.push({
                    product_id: i + 1,
                    product: `Product ${i + 1}`,
                    quantity: Math.floor(Math.random() * 100),
                    target: Math.floor(Math.random() * 100),
                });
            }
            return data;
        },
    }
});

// Ref and Variables
const datagridRef = ref();
const dataSelected = ref([]);
var itemSelected = computed(() => dataSelected.value.length > 0);

// ========================================================================
// If you want to use server side processing
// ========================================================================
// const dataKey = 'user_id'; //change to data primary key
// const dataRoute = route('user.data_processing') //change to data processing route
// const dataSource = new CustomStore({
//     key: dataKey,
//     load: dxLoad(dataRoute).bind(this),
// });

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
