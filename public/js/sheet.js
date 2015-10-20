$(document).ready(function(){

    function Sheet(){

        var json = {};
        var entityType = ['Publication'];
        var surface = $('#surface');

        /**
         * Initial Sheet class
         */
        this.init = function(){
            json = $.parseJSON($('#jsonSheet').val());
            var ent = '';
            for (var entity in json) {
                if (json[entity].length < 1) continue;
                ent = (/^\d+$/.test(Object.keys(json[entity])[0]) ? 'RePost' : entityType[parseInt(json[entity]['entity_type'])-1]);
                this['render'+ent](json[entity]);
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

        /**
         * Rendering publication
         * @param data
         */
        this.renderPublication = function(data){
            var ext = {};
            ext.pCreated = this.createElement('p', {'class':'created'}).text('Created: ').append(this.createElement('span').text('10 October 2015 09:40'));
            ext.totalComments = this.createElement('p', {'class':'abs totalComments'}).text('Total count of comments: ').append(this.createElement('span').text('22'));
            /*ext.addLike = this.createElement('div', {'class':'addLike'});
            ext.likesCount = this.createElement('div', {'class':'likesCount'}).append(this.createElement('a').text('5'));
            ext.addRePost = this.createElement('div', {'class':'addRePost'});
            ext.rePostsCount = this.createElement('div', {'class':'rePostsCount'}).append(this.createElement('a').text('10'));
            ext.addComment = this.createElement('div', {'class':'addComment'});
            ext.commentsCount = this.createElement('div', {'class':'commentsCount'}).append(this.createElement('a').text('27'));
            ext.reviews = this.createElement('div', {'class':'reviews'});
            ext.reviewsCount = this.createElement('div', {'class':'reviewsCount'}).append(this.createElement('a').text('11'));*/
            var elArr = ['addLike', 'likesCount', 'addRePost', 'rePostsCount', 'addComment', 'commentsCount', 'reviews', 'reviewsCount'];
            for (var a = 0; a < elArr.length; a++) {
                console.log(a%2);
            }

            var divExtra = this.createElement('div', {'class':'extra'});
            for (var key in ext) {
                divExtra.append(ext[key]);
            }
            var content = this.createElement('div', {'class':'content'}).text("Though my answer is downvoted, it's still worth to know that there is no such thing as order of keys in javascript object. Therefore, in theory, any code build on iterating values can be inconsistent. One approach could be creating an object and to define setter which actually provides counting, ordering and so on, and provide some methods to access this fields. This could be done in modern browsers."),
                hr = this.createElement('hr'),
                mainDiv = this.createElement('div', {'class':'publication'}).appendList([content, hr, divExtra]);

            console.log(mainDiv.get());
            surface.append(mainDiv.get());
            throw Error();
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
