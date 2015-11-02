
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
                    return (typeof _data[key] == 'string' && _data[key].length > 0 ? _data[key].replace(/\r\n/g, '<br/>') : '');
                }],
            [['commentsArray'],
                function(key){
                    return (typeof _data[key] != 'undefined' && _data[key].constructor===Array ? _data[key] : []);
                }],
            [['u_initials'],
                function(key){
                    var ba = [key, 'u_uid'];
                    for (var k in ba) if (typeof _data[ba[k]] == 'undefined') return null;
                    return '<a href="/user/'+_data[ba[1]]+'">'+_data[ba[0]]+'</a>';
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
}