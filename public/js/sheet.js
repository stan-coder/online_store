$(document).ready(function(){

    function Sheet(){

        var _json = {};
        var _entityType = ['Publication'];
        var _surface = $('#surface');
        var _data = {};
        var _prepData = {};

        /**
         * Initial Sheet class
         */
        this.init = function(){
            _json = $.parseJSON($('#jsonSheet').val());
            var ent = null;
            for (var entity in _json) {
                if (_json[entity].length < 1) continue;
                ent = (/^\d+$/.test(Object.keys(_json[entity])[0]) ? 'RePost' : _entityType[parseInt(_json[entity]['entity_type'])-1]);
                _data = _json[entity];
                this['render'+ent](_data);
            }
            (new Entity()).init();
        };

        /**
         * Determine entity by given index
         * @param index
         * @returns {string}
         */
        this.determineEntityByIndex = function(index){
            return 'as';
        };

        this.prepareData = function(){
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
                [['other_owner_en_u_id'],
                    function(key){
                        var ba = [key, 'u_initials', 'u_uid'];
                        for (var k in ba) {
                            if (typeof _data[ba[k]] == 'undefined') return null;
                        }
                        return '<a href="/user/'+_data[ba[2]]+'">'+_data[ba[1]]+'</a>';
                    }]];

            _prepData = {
                methods: [],
                get: function(key){
                    return (typeof this[key] != 'undefined' ? this.methods[this[key]](key) : undefined);
                }
            };
            var curArr = [];
            for (var key1 in initArr) {
                curArr = initArr[key1][0];
                _prepData.methods.push(initArr[key1][1]);
                for (var key2 in curArr) {
                    _prepData[curArr[key2]] = key1;
                }
            }
        };

        /**
         * Rendering publication
         * @param data
         */
        this.renderPublication = function(data){
            if (Object.keys(_prepData).length == 0) this.prepareData();
            var ext = {};
            ext.pCreated = this.createElement('p', {'class':'created'}).text('Created: ').append(this.createElement('span').text(_prepData.get('created')));
            ext.totalComments = this.createElement('p', {'class':'abs totalComments'}).text('Total count of comments: ').append(this.createElement('span').text(_prepData.get('total_comments')));
            var elArr = ['addLike', 'likesCount', 'addRePost', 'rePostsCount', 'addComment', 'commentsCount', 'reviews', 'reviewsCount'];
            for (var a = 0; a < elArr.length; a++) {
                ext[elArr[a]] = this.createElement('div', {'class':elArr[a]});
                if (a%2 == 1) ext[elArr[a]].append(this.createElement('a').text(_prepData.get(elArr[a].slice(0, -5).toLowerCase())));
            }
            if (parseInt(_prepData.get('liked_by_cur_user'))===1) $(ext.addLike.get()).get(0).className = 'isLiked';
            if (parseInt(_prepData.get('reposted_by_cur_user'))===1) $(ext.addRePost.get()).css('background', 'url("/public/img/shareDone.png")');

            var divExtra = this.createElement('div', {'class':'extra'});
            for (var key in ext) {
                divExtra.append(ext[key]);
            }
            var content = this.createElement('div', {'class':'content'}),
                hr = this.createElement('hr'),
                mainDiv = this.createElement('div', {'class':'publication entity', id:_prepData.get('entity_id')}).appendList([content, hr, divExtra]);

            content.get().innerHTML = _prepData.get('content') + (_prepData.get('other_owner_en_u_id')!==null?'<br/> Posted by: '+_prepData.get('other_owner_en_u_id'):'');
            _surface.append(mainDiv.get());
            //console.log(data);
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
