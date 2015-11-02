
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

        var content = this.createElement('div', {'class':'content'}),
            mainDiv = this.createElement('div', {'class':'publication entity', id:data.get('entity_id')}).appendList([content].concat(this.getExtraInformation(data)));
        content.get().innerHTML = data.get('content') + (data.get('u_initials')!==null?'<br/>Suggested by: '+data.get('u_initials'):'');
        this.divSurface.append(mainDiv.get());
    };

    /**
     * Rendering rePost
     * @param entityInfo
     */
    this.renderRePost = function(entityInfo, originalEntity, rePostedElements){
        var mainDiv = this.createElement('div', {'class':'rePost entity', id:entityInfo.get('entity_id')});

        if (rePostedElements.length>3) mainDiv.append(this.createPagination(rePostedElements));
        if (rePostedElements.length>0) this.createUsersRePosts(mainDiv, rePostedElements);
        var content = this.createElement('div', {'class':'content'}),
            commentByGroupAdmin = this.createElement('div', {'class':'commentByGroupAdmin'}).append(this.createElement('span').text('Comment to repost')).text(': '+entityInfo.get('content'));

        content.append(commentByGroupAdmin).get().innerHTML += originalEntity.content + ('u_initials' in originalEntity?'<br/>Suggested by: <a href="'+originalEntity['u_uid']+'">'+originalEntity['u_initials']:'');
        var descrOrigEn = this.createElement('div', {'class':'descriptionOriginalEntity'})
                .append( this.createElement('span').text('Repost was taken from')).text(': ')
                .append( this.createElement('a', {href:'/'+(originalEntity['source_entity_type']=='1'?'group':'user')+'/'+originalEntity['source_uid']}).text(originalEntity['source_info']))
                .append( this.createElement('span', {'class':'created'}).text(originalEntity['original_entity_created']) );

        content.append(descrOrigEn);
        mainDiv.appendList([content].concat(this.getExtraInformation(entityInfo)));
        this.divSurface.append(mainDiv.get());
    };

    /**
     * Create and return user previous rePosts
     * @param data
     */
    this.createUsersRePosts = function(mainDiv, data){
        for (var a=0; a<3; a++) {
            var div = this.createElement('div', {'class':'customCommentWhileRePost content'}).text(data[a]['descr_sub_rp']);
        }
    };

    /**
     * Create and return pagination based on granted data
     * @param data
     * @returns {Element}
     */
    this.createPagination = function(data){
        var pageCount = Math.ceil(data.length/3);
        var div = this.createElement('div', {'class':'rePostPagination content'});
        var ul = this.createElement('ul', {'class':'pagination pagination-sm'});
        var prevOrNext = this.createElement('li').append( this.createElement('a').append( this.createElement('span').text('«')));
        ul.append(prevOrNext);

        for (var a=1; a<=pageCount; a++) {
            ul.append( this.createElement('li', (a==1?{'class':'active'}:undefined)).append( this.createElement('a').text(a.toString())));
        }
        ul.append($(prevOrNext.get()).clone().find('span').text('»').parents('li'));
        div.append(ul);
        return div;
    };

    /**
     * Get elements describing extra information
     * @param data
     * @returns {*[]}
     */
    this.getExtraInformation = function(data){
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
        var hr = this.createElement('hr');
        return [hr, divExtra];
    };
}