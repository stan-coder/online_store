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
        $.ajax({
            url: '/entity/ajax/like',
            data: {
                entityId: e.detail.entityId
            },
            success: function(data){
                if (data.success !== true) {
                    var message = (typeof data.message == 'string' ? data.message : 'Unknown error')
                    this.showAlert(message);
                }
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
        ], function(tg){
            return {
                detail: {
                    entityId: $(tg).parents('.entity').attr('id'),
                    target: tg
                },
                bubbles: false,
                cancelable: true
            }
        }, this);
        eventManager.listen();
    };
}
