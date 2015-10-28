/**
 * This class contains basic methods to perform fundamental actions
 */

function Control(){

    var _hash = null;
    var _instance = this;

    /**
     * Init control class
     */
    this.initControl = function(){
        _hash = $('#hash').val();
        $.ajaxSetup({
            type: 'POST',
            dataType: 'json',
            context: this,
            timeout: 3000,
            beforeSend: function(jqxhr, settings){
                var hs = this.getSaltedHash();
                settings.data += '&hash='+hs[0]+'&salt='+hs[1];
                if (_instance.hasOwnProperty('ajaxExists')) jqxhr.abort();
                _instance.ajaxExists = true;
            },
            complete: function(){
                delete _instance.ajaxExists;
            },
            error: function(){
                this.showAlert('Unknown error was occured');
            }
        });
    };

    /**
     * Get array contains salted hash and appropriate salt
     * @returns {*[]}
     */
    this.getSaltedHash = function(){
        var salt = (new Date()).getTime();
        for (var h=0; h<10; h++) {
            salt += Math.random().toString();
        }
        salt = sha512(salt).substr(0, 20);
        return [sha512(_hash + salt).substr(0, 50), salt];
    };

    /**
     * Show message
     * @param message
     */
    this.showAlert = function(message){
        alert(message);
    };

    /**
     * Create element and set attributes filled values
     * @param tag
     * @param args
     * @returns {Element}
     */
    this.createElement = function(tag, args){
        var element = document.createElement(tag);
        if (args && args.constructor == Object) {
            var keys = Object.keys(args);
            for (var key in keys) {
                element.setAttribute(keys[key], args[keys[key]]);
            }
        }
        return (new this.constructorElement()).setElement(element);
    };

    /**
     * Constructor element
     */
    this.constructorElement = function(){
        var _element;
        this.setElement = function(element){
            _element = element;
            return this;
        };
        this.get = function(){
            return _element;
        };
        this.text = function(text){
            _element.appendChild(document.createTextNode(text));
            return this;
        };
        this.append = function(child){
            _element.appendChild(child.constructor == this.constructor ? child.get() : child);
            return this;
        };
        this.appendList = function(list){
            for (var key in list) {
                this.append(list[key]);
            }
            return this;
        };
    };

    this.isAjax = function(){
        return _instance.ajaxExists;
    };
}