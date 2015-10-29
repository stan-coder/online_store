
function entityModel() {

    this.getEventsList = function (controller) {
        return [[
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
        }, controller];
    };

    this.doLike = function (_data) {
        $.ajax({url: '/entity/ajax/like', data: _data});
    };
}
