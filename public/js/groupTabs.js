$(document).ready(function(){

    var tabs = function(){

        var tabsId = ['sheet', 'info', 'users', 'newUsers'];
        var loadedTabs = [];
        var groupId = 0;
        var hash = null;

        /**
         * Swithing tabs
         * @param tabId
         */
        this.switchTabs = function(tabId){
            var upId = tabId.substr(0, 1).toUpperCase() + tabId.substr(1);
            if (loadedTabs.indexOf(tabId) !== -1) {
                //upId
            }
            var url = '/groups/ajax/get' + upId;
            $.ajax({
                url: url,
                data: {
                    groupId: groupId,
                    name: 'Root',
                    hash: hash
                }
            });
        };

        /**
         * Init tabs control
         */
        this.init = function(){
            groupId = $('#groupId').val();
            hash = $('#hash').val();
            $('#groupTabs > li').on('click', this, function(event){
                if ($(this).hasClass('active') == false) {
                    $(this).siblings().removeClass('active');
                    $(this).addClass('active');
                    event.data.switchTabs($(this).get(0).id);
                }
            });

            $.ajaxSetup({
                type: 'POST',
                dataType: 'json'
            });
        };

        /**
         * Show tab info about group
         * @param json
         */
        this.showTabInfo = function(json){
            console.log(json);
        }
    };

    (new tabs()).init();
});