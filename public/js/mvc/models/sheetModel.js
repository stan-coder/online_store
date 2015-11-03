
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

    this.addPublication = function (data) {
        $.ajax({
            url: '/sheet/ajax/addPublication',
            data: data
        });
    }
}