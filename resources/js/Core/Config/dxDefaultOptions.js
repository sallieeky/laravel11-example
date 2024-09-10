import { exportDataGrid } from 'devextreme/excel_exporter';
import dxDataGrid from 'devextreme/ui/data_grid';
import { Workbook } from 'exceljs';
import { saveAs } from 'file-saver';

// Set global default options for dxDataGrid
dxDataGrid.defaultOptions({
    options: {
        noDataText: 'No data available',
        columnAutoWidth: true,
        remoteOperations: {
            paging: true,
            filtering: true,
            sorting: true,
        },
        itemPerPage: 10,
        hoverStateEnabled: true,
        filterRow: {
            visible: true,
            applyFilter: 'auto'
        },
        export: {
            enabled: true,
        },
        onExporting: function(e) {
            const workbook = new Workbook();
            const worksheet = workbook.addWorksheet('Main sheet');
            let fileName = new Date().getFullYear() + '_' + (new Date().getMonth() + 1) + '_' + new Date().getDate() + '_' + new Date().getHours() + new Date().getMinutes() + new Date().getSeconds() + '_data';

            exportDataGrid({
                component: e.component,
                worksheet,
                autoFilterEnabled: true,
            }).then(() => {
                workbook.xlsx.writeBuffer().then((buffer) => {
                    saveAs(new Blob([buffer], { type: 'application/octet-stream' }), fileName + '.xlsx');
                });
            });

            e.cancel = true;
        },
        selection: {
            selectAllMode: 'page',
            showCheckBoxesMode: 'always',
            mode: 'multiple',
        },
        columnChooser: {
            enabled: true,
            mode: 'select'
        },
        headerFilter: {
            visible: false
        },
        paging: {
            pageSize: 10
        },
        pager: {
            visible: true,
            allowedPageSizes: [10, 20, 50],
            showPageSizeSelector: true,
            showInfo: true
        },
        showBorders: true,
    }
});
