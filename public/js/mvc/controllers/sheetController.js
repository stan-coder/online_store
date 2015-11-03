
function sheetController() {

    var _data = {};
    var _prepData = {};
    var self = this;

    /**
     * Index action
     */
    this.index = function () {
        var _json = this.model.getJsonSheet(this.view.inputJson);
        var entity;
        var _entityTypes = this.model.getEntityTypes();
        for (var key in _json) {
            if (_json[key].length < 1) continue;
            entity = (/^\d+$/.test(Object.keys(_json[key])[0]) ? 'RePost' : _entityTypes[parseInt(_json[key]['entity_type'])-1]);
            _data = _json[key];
            self['set'+entity](_data);
        }
        (new entityController()).index(this);
        var io, event;
        for (key in self) {
            io = key.indexOf('SheetEvent');
            if (io > 0 && key.substr(io+10) && self[key].constructor===Function) {
                event = key.substr(io+10).toLowerCase();
                self.view[key.substr(0, io)].on(event, self, self[key]);
            }
        }
    };

    /**
     * Method creates object that helps to retrieve correct data from received json
     */
    this.prepareData = function(data){
        _data = data || _data;
        var initArr = [[
            ['comments', 'likes', 'reviews', 'reposts', 'total_comments'],
            function(key){
                return (typeof _data[key] != 'undefined' && /^\d+$/.test(_data[key]) ? _data[key] : 0);
            }],
            [['entity_type', 'entity_id', 'liked_by_cur_user', 'reposted_by_cur_user'],
                function(key){
                    return (typeof _data[key] != 'undefined' ? _data[key] : null);
                }],
            [['created'],
                function(key){
                    return (typeof _data[key] != 'undefined' ? _data[key] : 'not_specified');
                }],
            [['content'],
                function(key){
                    return (typeof _data[key] == 'string' && _data[key].length > 0 ? _data[key].replace(/(\r)?\n/g, '<br/>') : '');
                }],
            [['commentsArray'],
                function(key){
                    return (typeof _data[key] != 'undefined' && _data[key].constructor===Array ? _data[key] : []);
                }],
            [['u_initials'],
                function(key){
                    var ba = [key, 'u_uid'];
                    for (var k in ba) if (typeof _data[ba[k]] == 'undefined') return null;
                    return '<a href="/profile/'+_data[ba[1]]+'">'+_data[ba[0]]+'</a>';
                }]];

        _prepData = {
            methods: [],
            get: function(key){ // key like "created"
                return (typeof this[key] != 'undefined' ? this.methods[this[key]](key) : undefined);
            }
        };
        var buffer;
        for (var key1 in initArr) {
            buffer = initArr[key1][0];
            _prepData.methods.push(initArr[key1][1]);
            for (var key2 in buffer) {
                _prepData[buffer[key2]] = key1; // comments = 0; ...
            }
        }
        return _prepData;
    };

    /**
     * Setting publications
     */
    this.setPublication = function () {
        if (Object.keys(_prepData).length == 0) this.prepareData();
        this.view.renderPublication(_prepData);
    };

    /**
     * Setting rePosts
     */
    this.setRePost = function (data) {
        var elementsCount = data.constructor == Object ? Object.keys(data).length-1 : data.length;
        var origEnt = data[elementsCount-1];
        origEnt.content = this.prepareData({content: origEnt.content}).get('content');
        for (var key in origEnt) {
            if (key.indexOf('_single')>=0) {
                var bf = origEnt[key];
                origEnt[key.slice(0, -7)] = bf;
                delete origEnt[key];
            }
        }
        this.view.renderRePost(this.prepareData(data[0]), origEnt, this.getRePostedElements(data, elementsCount));
    };

    /**
     * Get elements of rePost data that contains comments other users
     */
    this.getRePostedElements = function(data, elementsCount) {
        if (!$.isArray(data)) {
            var bf = [];
            for (var key in data) {
                if ($.isNumeric(key)) bf.push(data[key]);
            }
            data = bf;
        }
        if (elementsCount===2) return [];
        return data.slice(1, -1);
    };

    /**
     * Event adding new record (i.e. so called "entity")
     */
    this.aAddRecordSheetEventClick = function (e) {
        self.view.divAddNEntWrapper.toggle('fast', function () {
            var ta = self.view.textAreaRecord;
            if ($(e.target).parent().next().css('display')=='block') $(ta).focus();
            else $(ta).val('');
        });
    };

    /**
     * Click button to public new record
     */
    this.buttonPublicRecordSheetEventClick = function (e) {
        var ta = self.view.textAreaRecord;
        if (ta.val().length > 0) {
            self.view.blockAddRecord(true);
            self.originInstance.additionAjaxComplete(function(){
                self.view.blockAddRecord(false);
            });
            self.originInstance.getDeferred().done(function (data) {
                self.view.renderPublication(
                    self.prepareData({
                        content: self.htmlSpecialChars(self.stripTags(ta.val())),
                        created: data.created,
                        entity_id: data.pId
                }), true);
            });
            var _data = {content:ta.val()};
            self.model.addPublication(_data);
        }
        ta.focus();
    };

    /**
     * Strip any tag
     * @param input
     * @returns {XML|string}
     */
    this.stripTags = function (input) {
        var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi, commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
        return input.replace(commentsAndPhpTags, '').replace(tags, '');
    };

    /**
     * Convert some symbol to html representation
     * @param string
     * @returns {*}
     */
    this.htmlSpecialChars = function (string) {
        var stack = {'&':'&amp;', '"':'&quot;', "'":'&#039;', '<':'&lt;', '>':'&gt;'};
        for (var symbol in stack) {
            string = string.replace((new RegExp(symbol, 'g')), stack[symbol]);
        }
        return string;
    };
}