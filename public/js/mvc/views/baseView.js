
function baseView() {

    /**
     * Show message
     * @param message
     */
    this.showAlert = function(message){
        //alert(message);
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
            if (child instanceof jQuery) child = child.get(0);
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
}