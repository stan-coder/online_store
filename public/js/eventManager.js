/**
 * Event Manager class provides access to event
 * @constructor
 */

function EventManager() {

    var _attList = [];
    var _body = document.getElementsByTagName('body')[0];

    /**
     * To fill "attach list" by events
     * @param events
     * @param customEventObject
     * @param evokedClass
     */
    this.attachList = function(events, customEventObject, evokedClass){
        _attList.push([events, customEventObject, evokedClass]);
    };

    /**
     * Start listening event on whole body including is not created element yet
     */
    this.listen = function(){
        var ca = [];
        var bf;
        var bfResult = [];
        var ev;
        for (var key1 in _attList) {
            ca = _attList[key1][0];
            bf = [];
            for (var key2 in ca) {
                ev = ca[key2][0];
                _body.addEventListener(ev, _attList[key1][2][ev], false);
                bf.push([ev, ca[key2][1]]);
            }
            bfResult.push([bf, _attList[key1][1]]);
        }
        _body.addEventListener('click', function(event){
            for (var key3 in bfResult) {
                bf = bfResult[key3][0];
                for (var key4 in bf) {
                    if (bf[key4][1](event.target)===true) {
                        event.preventDefault();
                        var csEvent = new CustomEvent(bf[key4][0], bfResult[key3][1](event.target));
                        event.currentTarget.dispatchEvent(csEvent);
                    }
                }
            }
        });
    };
}