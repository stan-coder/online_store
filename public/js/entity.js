/**
 * This class is established to provide access to basic actions any entities on the sheet
 * @constructor
 */

function Entity(){

    /**
     * Arr new like
     * @param e
     */
    this.like = function(e){
        var _data = {
            entityId: e.detail.entityId
        };
        if (e.detail.target.className == 'isLiked') {
            _data.reject = true;
        }
        $.ajax({
            url: '/entity/ajax/like',
            data: _data,
            success: function(data){
                if (data.success !== true) {
                    var message = (typeof data.message == 'string' ? data.message : 'Unknown error');
                    this.showAlert(message);
                }
                var lCn = $(e.detail.target).siblings('.likesCount').find('a');
                var cn = parseInt(lCn.text());
                var params = (_data.hasOwnProperty('reject') ? ['addLike', cn-1] : ['isLiked', cn+1]);
                e.detail.target.className = params[0];
                lCn.text(params[1]);
            }
        });
    };

    /**
     * Add new rePost
     * @param e
     */
    this.addRePost = function(e){
        console.log(e.detail.entityId + ' - rePost');
    };

    /**
     * Initialize Entity class
     */
    this.init = function(){
        var eventManager = new EventManager();
        eventManager.attachList([
            ['like', function(tg){
                return tg.className === 'addLike' || tg.className === 'isLiked';
            }],
            ['addRePost', function(tg){
                return tg.className === 'addRePost';
            }]
        ], function(tg, instance){
            return {
                detail: {
                    entityId: $(tg).parents('.entity').attr('id'),
                    target: tg,
                    instance: instance
                },
                bubbles: false,
                cancelable: true
            }
        }, this);
        eventManager.listen();
    };

    this.preExecMethods = function(customEvent){
        customEvent.detail.instance[customEvent.type](customEvent);
    };
}
