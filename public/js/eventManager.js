function EventManager() {

    var _attList = [];

    this.attachList = function(events, customEventObject, evokedClass){
        _attList.push([events, customEventObject, evokedClass]);
    };

    this.listen = function(){
        var ca = [];
        var bf = [];
        var ev;
        for (var key1 in _attList) {
            ca = _attList[key1][0];
            for (var key2 in ca) {
                ev = ca[key2][0];
                document.body.addEventListener(ev, _attList[key1][2][ev], false);
                bf.push([ev, ca[key2][1]]);
            }
        }
        document.getElementsByTagName('body')[0].addEventListener('click', function(event){
            for (var key3 in bf) {
                if (bf[key3][1](event.target)===true) {
                    event.preventDefault();
                    var csEvent = new CustomEvent(bf[key3][0], {
                        detail: {
                            entityId: $(event.target).parents('.entity').attr('id'),
                            target: event.target
                        },
                        bubbles: false,
                        cancelable: true
                    });
                    event.currentTarget.dispatchEvent(csEvent);
                }
            }
        });
    };
}