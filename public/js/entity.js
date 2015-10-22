/**
 * This class is established to provide access to basic actions any entities on the sheet
 */

function Entity(){

    this.addLike = function(e){
        console.log(e.detail.entityId + ' - like');
    };

    this.addRePost = function(e){
        console.log(e.detail.entityId + ' - rePost');
    };

    this.init = function(){
        var eventManager = new EventManager();
        eventManager.attachList([
            ['addLike', function(tg){
                return tg.className === 'addLike';
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

        /*document.body.addEventListener('click', function(event) {
            if (event.target.tagName.toLowerCase() === 'div' && event.target.className === 'addLike') {
                event.preventDefault();
                var likeEvent = new CustomEvent('addLike', {
                    detail: {
                        entityId: $(event.target).parents('.entity').attr('id'),
                        target: event.target
                    },
                    bubbles: false,
                    cancelable: true
                });
                event.currentTarget.dispatchEvent(likeEvent);
            }
            if (event.target.tagName.toLowerCase() === 'div' && event.target.className === 'addRePost') {
                event.preventDefault();
                var rePostEvent = new CustomEvent('addRePost', {
                    detail: {
                        entityId: $(event.target).parents('.entity').attr('id'),
                        target: event.target
                    },
                    bubbles: false,
                    cancelable: true
                });
                event.currentTarget.dispatchEvent(rePostEvent);
            }
        }, false);
        document.body.addEventListener('addLike', this.addLike, false);
        document.body.addEventListener('addRePost', this.addRePost, false);*/
    };
}












/*document.querySelector('body').addEventListener('click', function (e) {
    if (e.target.tagName.toLowerCase() !== 'a') return;

    e.preventDefault();
    var msg = document.getElementById('msg').value.trim();
    if (msg && window.CustomEvent) {
        var event = new CustomEvent('logMyMessageEvent', {
            detail: {
                message: msg,
                time: new Date()
            },
            bubbles: true,
            cancelable: true
        });
        e.currentTarget.dispatchEvent(event);
    }
}, false);

document.body.addEventListener('logMyMessageEvent', logMyMessageHandler, false);

function logMyMessageHandler(e) {
    document.getElementById('log').textContent += "Event subscriber on"+e.currentTarget.nodeName+", "+e.detail.time.toLocaleString()+":"+e.detail.message+"\n";
}*/
