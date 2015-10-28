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
        var buffer;
        for (var key1 in _attList) {
            buffer = _attList[key1][0];
            for (var key2 in buffer) {
                _body.addEventListener(buffer[key2][0], _attList[key1][2].preExecMethods, false);
            }
        }
        _body.addEventListener('click', function(event){
            for (var key3 in _attList) {
                buffer = _attList[key3][0];
                for (var key4 in buffer) {
                    if (buffer[key4][1](event.target)===true) {
                        event.preventDefault();
                        var csEvent = new CustomEvent(buffer[key4][0], _attList[key3][1](event.target, _attList[key3][2]));
                        event.currentTarget.dispatchEvent(csEvent);
                    }
                }
            }
        });
    };
}