<div style="width: 900px; margin: 0 auto;">
    <ul class="nav nav-tabs" id="groupTabs">
        <li role="presentation" class="active" id="sheet"><a>Sheet</a></li>
        <li role="presentation" id="info"><a>Info</a></li>
        <li role="presentation" id="users"><a>Users</a></li>
        <li role="presentation" id="newUsers"><a>New users</a></li>
    </ul>
    <div style="width: 100%; height: auto; border: 1px solid #ddd; border-top: none; padding: 30px 20px 30px 20px" id="surface">
        <p id="addRecord"><a>Add new record</a></p>
        <div id="addNEntWrapper" style="display: none">
            <div class="addNewEntity">
                <textarea id=""></textarea>
            </div>
            <div style="margin-bottom: 13px;">
                <button type="button" class="btn btn-default">Public</button>
            </div>
        </div>

        <div class="publication" style="display: none;">
            <div class="content">Though my answer is downvoted, it's still worth to know that there is no such thing as order of keys in javascript object. Therefore, in theory, any code build on iterating values can be inconsistent. One approach could be creating an object and to define setter which actually provides counting, ordering and so on, and provide some methods to access this fields. This could be done in modern browsers.</div>
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
    <input type="hidden" id="groupId" value="{groupId}"/>
    <input type="hidden" id="hash" value="{hash}"/>
    <input type='hidden' id='jsonSheet' value='{jsonSheet}'>
</div>