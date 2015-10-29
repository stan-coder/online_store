
$(document).ready(function () {
    function Origin() {
        this.url = [
            [/\/group\/\d{14}$/g, {
                models: ['sheet', 'entity'],
                controllers: ['sheet', 'entity', 'event'],
                views: ['sheet']
            }, 'sheet']
        ];
        var self = this;
        var _sCounter = 0;
        var _selectedControllers;
        var _indexController;
        var _hash = null;
        var _baseView;

        /**
         * Beginning origin class
         */
        this.start = function () {
            var href = document.location.href.replace(/#.*$/g, '');
            var script, curUrl, firstChr, srcName;
            for (var index in self.url) {
                if (self.url[index][0].test(href)) {
                    curUrl = self.url[index][1];
                    _indexController = self.url[index][2];
                    for (var key in curUrl) {
                        if (key=='controllers') {
                            _selectedControllers = curUrl[key];
                        }
                        for (var src in curUrl[key]) {
                            _sCounter++;
                            script = document.createElement('script');
                            firstChr = key.substr(0,1).toUpperCase();
                            srcName = curUrl[key][src]+firstChr+key.slice(0,-1).substr(1);
                            script.src = '/public/js/mvc/'+key+'/'+srcName+'.js';
                            document.getElementsByTagName('head')[0].appendChild(script);
                            $(script).on('load', this, function (e) {
                                _sCounter--;
                                if (_sCounter===0) {
                                    _baseView = new baseView();
                                    e.data.building();
                                }
                            });
                        }
                    }
                    break;
                }
            }
            this.initAjax();
        };

        /**
         * Building primary components
         */
        this.building = function () {
            var controller, cn;
            for (var key in _selectedControllers) {
                cn = _selectedControllers[key];
                controller = window[cn+'Controller'];
                controller.prototype = {
                    model: window.hasOwnProperty(cn+'Model') ? new window[cn+'Model'] : {},
                    view: window.hasOwnProperty(cn+'View') ? (function () {
                        var view = window[cn+'View'];
                        view.prototype = _baseView;
                        var viewExemplar = new view();
                        if (viewExemplar.hasOwnProperty('elements')) {
                            for (var element in viewExemplar.elements) {
                                viewExemplar[element] = viewExemplar.elements[element];
                            }
                        }
                        return viewExemplar;
                    })() : {},
                    originInstance: self
                };
            }
            (new window[_indexController+'Controller']).index();
        };

        /**
         * Init control class
         */
        this.initAjax = function(){
            _hash = $('#hash').val();
            $.ajaxSetup({
                type: 'POST',
                dataType: 'json',
                context: this,
                timeout: 3000,
                async: true,
                beforeSend: function(jqxhr, settings){
                    var hs = this.getSaltedHash();
                    settings.data += '&hash='+hs[0]+'&salt='+hs[1];
                    if (self.hasOwnProperty('ajaxExists')) jqxhr.abort();
                    self.ajaxExists = true;
                },
                success: function(data){
                    self.getDeferred().resolve(data);
                },
                complete: function(){
                    delete self.ajaxExists;
                    delete self.deferred;
                },
                error: function(){
                    _baseView.showAlert('Unknown error was occured');
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
         * Getting created deferred
         * @returns {*}
         */
        this.getDeferred = function () {
            if (!self.hasOwnProperty('deferred')) {
                self.deferred = $.Deferred();
            }
            return self.deferred;
        };
    }

    var origin = new Origin();
    origin.start();
});