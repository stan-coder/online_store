$(document).ready(function(){

    function Sheet(){

        var _json = {};
        var _entityType = ['Publication'];
        var _surface = $('#surface');
        var _data = {};
        var _preparedData = {};

        /**
         * Initial Sheet class
         */
        this.init = function(){
            _json = $.parseJSON($('#jsonSheet').val());
            var ent = '';
            for (var entity in _json) {
                if (_json[entity].length < 1) continue;
                ent = (/^\d+$/.test(Object.keys(_json[entity])[0]) ? 'RePost' : _entityType[parseInt(_json[entity]['entity_type'])-1]);
                _data = _json[entity];
                this['render'+ent](_data);
            }
        };

        /**
         * Determine entity by given index
         * @param index
         * @returns {string}
         */
        this.determineEntityByIndex = function(index){
            return 'as';
        };

        /** var o = {vl: '12'};

        Object.defineProperties(o, {

            'getName': {
                val: 'root',
                get: function(){
                    return this.val;
                }
            },
            'getSurname': {
                get: function(){
                    return this.vl;
                }
            }

        });
        console.log(o.getName);
        console.log(o.getSurname);
         https://developer.mozilla.org/ru/docs/Web/JavaScript/Reference/Global_Objects/Object/defineProperty
         https://developer.mozilla.org/en/docs/Web/JavaScript/Reference/Global_Objects/Object/defineProperties*/

        this.prepareAccessDataObject = function(){
            var initArr = [[
                ['comments', 'likes', 'reviews', 'reposts', 'total_comments'],
                function(key){
                    return (typeof _data[key] != 'undefined' && /^\d+$/.test(_data[key]) ? _data[key] : 0)
                }
            ], [['entity_type', 'entity_id', 'liked_by_cur_user', 'reposted_by_cur_user', 'content'],
                function(key){
                    return (typeof _data[key] != 'undefined' ? _data[key] : null);
                }
            ], [['created'],
                function(key){
                    console.log('as');
                    return (typeof _data[key] != 'undefined' ? _data[key] : 'not_specified');
                }
            ], [['other_owner_en_u_id'],
                function(key){
                    var ba = [key, 'u_initials', 'u_uid'];
                    for (var k in ba) {
                        if (typeof _data[ba[k]] == 'undefined') return null;
                    }
                    return '<a href="/user/'+_data[ba[2]]+'">'+_data[ba[1]]+'</a>';
                }
            ]];
            var buffer = {};
            var curArr = [];
            for (var key1 in initArr) {
                curArr = initArr[key1][0];
                for (var key2 in curArr) {
                    buffer[curArr[key2]] = {
                        enumerable: true,
                        configurable: false,
                        get: initArr[key1][1]
                    };
                }
            }
            Object.defineProperties(_preparedData, buffer);
        };

        /**
         * Rendering publication
         * @param data
         */
        this.renderPublication = function(data){
            if (Object.keys(_preparedData).length == 0) this.prepareAccessDataObject();
            //console.log(data);
            var ext = {};
            ext.pCreated = this.createElement('p', {'class':'created'}).text('Created: ').append(this.createElement('span').text(_preparedData.created));
            ext.totalComments = this.createElement('p', {'class':'abs totalComments'}).text('Total count of comments: ').append(this.createElement('span').text('22'));
            var elArr = ['addLike', 'likesCount', 'addRePost', 'rePostsCount', 'addComment', 'commentsCount', 'reviews', 'reviewsCount'];
            for (var a = 0; a < elArr.length; a++) {
                ext[elArr[a]] = this.createElement('div', {'class':elArr[a]});
                if (a%2 == 1) ext[elArr[a]].append(this.createElement('a').text('10'));
            }
            var divExtra = this.createElement('div', {'class':'extra'});
            for (var key in ext) {
                divExtra.append(ext[key]);
            }
            var content = this.createElement('div', {'class':'content'}).text(data.content),
                hr = this.createElement('hr'),
                mainDiv = this.createElement('div', {'class':'publication'}).appendList([content, hr, divExtra]);

            _surface.append(mainDiv.get());
            //throw Error();
        };

        /**
         * Rendering rePost
         * @param data
         */
        this.renderRePost = function(data){

        };
    }

    Sheet.prototype = new Control();
    (new Sheet()).init();
});
