<div style="width: 900px; margin: 0 auto;">
    <ul class="nav nav-tabs" id="groupTabs">
        <li role="presentation" class="active" id="sheet"><a>Sheet</a></li>
        <li role="presentation" id="info"><a>Info</a></li>
        <li role="presentation" id="users"><a>Users</a></li>
        <li role="presentation" id="newUsers"><a>New users</a></li>
    </ul>
    <div id="surface">
        <?php if (!empty(controllerManager::$variables['addRecord'])) : ?>
        <p id="addRecord"><a>Add new record</a></p>
        <div id="addNEntWrapper">
            <div class="addNewEntity">
                <textarea></textarea>
            </div>
            <div style="margin-bottom: 13px;">
                <button type="button" id="publicRecord" class="btn btn-default">Public</button>
            </div>
        </div>
        <?php endif; ?>

        <div class="rePost entity"  style="display: none;">
            <div class="rePostPagination content">
                <ul class="pagination pagination-sm">
                    <li><a><span>«</span></a></li>
                    <li><a>1</a></li>
                    <li><a>2</a></li>
                    <li><a>3</a></li>
                    <li><a>4</a></li>
                    <li><a>5</a></li>
                    <li><a><span>»</span></a></li>
                </ul>
                <span class="hint">Pagination to nested reposts</span>
            </div>
            <div class="customCommentWhileRePost content">
                This is comment to rePost
                <div>Reposted by <a href="">Galina Xenova</a></div>
                <span>24 June 2015 21:51</span>
            </div>
            <div class="customCommentWhileRePost content">
                This is comment to rePost
                <div>Reposted by <a href="">Galina Xenova</a></div>
                <span>24 June 2015 21:51</span>
            </div>
            <div class="content">
                <div class="commentByGroupAdmin">
                    <span>Comment to repost</span>: This is comment added by group's admin
                </div>
                Though my answer is downvoted, it's still worth to know that there is no such thing as order of keys in javascript object. Therefore, in theory, any code build on iterating values can be inconsistent. One approach could be creating an object and to define setter which actually provides counting, ordering and so on, and provide some methods to access this fields. This could be done in modern browsers.
                <div class="descriptionOriginalEntity">
                    <span>Repost was taken from</span>: <a href="/group/30937305030562">Группа про крылья</a><br/>
                    <span class="created">19 October 2015 17:22</span>
                </div>
            </div>
            <hr/>
            <div class="extra">
                <p class="created">Created: <span>10 October 2015 09:40</span></p>
                <p class="abs totalComments">Total count of comments: <span>22</span></p>

                <div class="addLike"></div>
                <div class="likesCount"><a>5</a></div>

                <div class="addRePost"></div>
                <div class="rePostsCount"><a>10</a></div>

                <div class="addComment"></div>
                <div class="commentsCount"><a>27</a></div>

                <div class="reviews"></div>
                <div class="reviewsCount"><a>11</a></div>
            </div>
        </div>

        <!--<div class="comments">
            <div class="add">
                <textarea id=""></textarea>
            </div>
            <div class="comment">
                <div class="userIcon"></div>
                <div class="body">
                    <p class="commentContent">One approach could be creating an object and to define setter which actually provides counting, ordering and so on and provide some methods.</p>
                    <div class="action">
                        <a class="author">Stanislav Zavalishin</a>
                        <a>Like</a><a class="num likeCount">15</a>
                        <a>Reply</a><a class="num replyCount">62</a>
                        <span class="created">12 Sept 2014 09:40</span>
                    </div>
                </div>
            </div>
            <div class="comment">
                <div class="userIcon"></div>
                <div class="body">
                    <p class="tx">One approach could be creating an object and to define setter which actually provides counting, ordering and so on and provide some methods.</p>
                    <div class="action">
                        <a class="author">Stanislav Zavalishin</a>
                        <a>Like</a><a class="num likeCount">15</a>
                        <a>Reply</a><a class="num replyCount">62</a>
                        <span class="created">12 Sept 2014 09:40</span>
                    </div>
                </div>
            </div>
        </div>-->

    </div>
    <input type="hidden" id="sheetEntityId" value="{sheetEntityId}"/>
    <input type="hidden" id="token" value="{token}"/>
    <input type="hidden" id="salt" value="{salt}"/>
    <input type='hidden' id='jsonSheet' value='{jsonSheet}'>
</div>