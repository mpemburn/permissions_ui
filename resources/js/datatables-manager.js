export default class DatatablesManager {
    constructor() {
    }

    run(tableId, options) {
        this.tableId = tableId;
        this.table = $('#' + this.tableId);
        this.table.DataTable(options);

        $('[name="' + this.tableId + '_length"]').addClass('dt-custom');
    }
}
