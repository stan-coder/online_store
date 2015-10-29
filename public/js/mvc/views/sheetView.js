
function sheetView() {

    this.elements = {
        inputJson: $('#jsonSheet'),
        divSurface: $('#surface')
    };

    /**
     * Rendering publication
     * @param data
     */
    this.renderPublication = function(data){
        var ext = {};
        ext.pCreated = this.createElement('p', {'class':'created'}).text('Created: ').append(this.createElement('span').text(data.get('created')));
        ext.totalComments = this.createElement('p', {'class':'abs totalComments'}).text('Total count of comments: ').append(this.createElement('span').text(data.get('total_comments')));
        var elArr = ['addLike', 'likesCount', 'addRePost', 'rePostsCount', 'addComment', 'commentsCount', 'reviews', 'reviewsCount'];
        for (var a = 0; a < elArr.length; a++) {
            ext[elArr[a]] = this.createElement('div', {'class':elArr[a]});
            if (a%2 == 1) ext[elArr[a]].append(this.createElement('a').text(data.get(elArr[a].slice(0, -5).toLowerCase())));
        }
        if (parseInt(data.get('liked_by_cur_user'))===1) $(ext.addLike.get()).get(0).className = 'isLiked';
        if (parseInt(data.get('reposted_by_cur_user'))===1) $(ext.addRePost.get()).css('background', 'url("/public/img/shareDone.png")');

        var divExtra = this.createElement('div', {'class':'extra'});
        for (var key in ext) {
            divExtra.append(ext[key]);
        }
        var content = this.createElement('div', {'class':'content'}),
            hr = this.createElement('hr'),
            mainDiv = this.createElement('div', {'class':'publication entity', id:data.get('entity_id')}).appendList([content, hr, divExtra]);

        content.get().innerHTML = data.get('content') + (data.get('other_owner_en_u_id')!==null?'<br/> Posted by: '+data.get('other_owner_en_u_id'):'');
        this.divSurface.append(mainDiv.get());
        //console.log(data);
    };

    /**
     * Rendering rePost
     * @param data
     */
    this.renderRePost = function(data){

    };
}