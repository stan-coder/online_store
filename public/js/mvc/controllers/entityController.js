/**
 * This class is established to provide access to basic actions any entities on the sheet
 * @constructor
 */

function entityController() {

    var _sheetView;
    var self = this;

    /**
     * Index action
     */
    this.index = function(sheetController){
        _sheetView = sheetController.view;
        var events = new eventController();
        events.attachList.apply(null, this.model.getEventsList(this));
        events.listen();
    };

    /**
     * Methods that check is there an active ajax process
     * @param customEvent
     */
    this.preExecMethods = function(customEvent){
        if (!self.originInstance.hasOwnProperty('ajaxExists')) {
            customEvent.detail.instance[customEvent.type](customEvent);
        }
    };

    /**
     * Add or remove like
     * @param e
     */
    this.like = function(e){
        var _data = {
            entityId: e.detail.entityId
        };
        if (e.detail.target.className == 'isLiked') {
            _data.reject = true;
        }
        self.originInstance.getDeferred().done(function (data){
            var lCn = $(e.detail.target).siblings('.likesCount').find('a');
            var cn = parseInt(lCn.text());
            var params = (_data.hasOwnProperty('reject') ? ['addLike', cn-1] : ['isLiked', cn+1]);
            e.detail.target.className = params[0];
            lCn.text(params[1]);
        });
        this.model.doLike(_data);

    };

    /**
     * Add new rePost
     * @param e
     */
    this.addRePost = function(e){
        console.log(e.detail.entityId + ' - rePost');
    };
}