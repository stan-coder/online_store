
function sheetModel() {

    /**
     * Get general json
     * @param element
     */
    this.getJsonSheet = function (element) {
        return $.parseJSON(element.val());
    };

    this.getEntityTypes = function () {
        return ['Publication'];
    };
}