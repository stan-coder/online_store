<div style="width: 900px; margin: 0 auto;">
    <ul class="nav nav-tabs" id="groupTabs">
        <li role="presentation" class="active" id="sheet"><a>Sheet</a></li>
        <li role="presentation" id="info"><a>Friends</a></li>
        <li role="presentation" id="users"><a>Groups</a></li>
    </ul>
    <div id="surface">
        <?php if (isset(controllerManager::$variables['addRecord'])) : ?>
        <p id="addRecord"><a>Add new record</a></p>
        <div id="addNEntWrapper" style="display: none">
            <div class="addNewEntity">
                <textarea></textarea>
            </div>
            <div style="margin-bottom: 13px;">
                <button type="button" id="publicRecord" class="btn btn-default">Public</button>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <input type="hidden" id="sheetEntityId" value="{sheetEntityId}"/>
    <input type="hidden" id="token" value="{token}"/>
    <input type="hidden" id="salt" value="{salt}"/>
    <input type='hidden' id='jsonSheet' value='{jsonSheet}'>
</div>