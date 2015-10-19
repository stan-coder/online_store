<div style="width: 900px; margin: 0 auto;">
    <ul class="nav nav-tabs" id="groupTabs">
        <li role="presentation" class="active" id="sheet"><a>Sheet</a></li>
        <li role="presentation" id="info"><a>Info</a></li>
        <li role="presentation" id="users"><a>Users</a></li>
        <li role="presentation" id="newUsers"><a>New users</a></li>
    </ul>
    <div style="width: 100%; height: 90%; border: 1px solid #ddd; border-top: none; padding: 30px 20px 30px 20px" id="surface">
        <div class="publication">
            <div class="content">
                Though my answer is downvoted, it's still worth to know that there is no such thing as order of keys in javascript object. Therefore, in theory, any code build on iterating values can be inconsistent. One approach could be creating an object and to define setter which actually provides counting, ordering and so on, and provide some methods to access this fields. This could be done in modern browsers.
            </div>
            <hr/>
            <div class="extra">
                <p class="created">Created: <span>2015-10-10 09:40:29</span></p>
                <p class="abs likes">Likes: <span>5</span></p>
                <p class="abs rePosts">Reposts: <span>10</span></p>
                <p class="abs comments">Comments: <span>12</span></p>
                <p class="abs totalComments">Total count of comments: <span>22</span></p>
            </div>
        </div>
    </div>
    <input type="hidden" id="groupId" value="{groupId}"/>
    <input type="hidden" id="hash" value="{hash}"/>
    <input type='hidden' id='jsonSheet' value='{jsonSheet}'>
</div>