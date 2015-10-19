$(document).ready(function(){

    function Tabs(){

        var tabsId = ['sheet', 'info', 'users', 'newUsers'];
        var loadedTabs = [];
        var groupId = 0;
        var tabSurface = $('#surface');

        /**
         * Switching tabs
         * @param tabId
         */
        this.switchTabs = function(tabId){
            if (tabsId.indexOf(tabId) === -1) {
                this.showAlert('A tab has incorrect parameters');
                return;
            }
            document.location.hash = tabId;
            var upId = tabId.substr(0, 1).toUpperCase() + tabId.substr(1);
            var tbs = $('div[id^=tab]').hide();
            if (loadedTabs.indexOf(upId) !== -1) {
                tbs.filter(function(){
                    return $(this).get(0).id == 'tab'+upId;
                }).show();
                return;
            }
            var url = '/groups/ajax/get' + upId;
            $.ajax({
                url: url,
                data: {
                    groupId: groupId
                },
                success: function(data){
                    if (!data.success) {
                        this.showAlert('There is an error as a result of request');
                        return;
                    }
                    loadedTabs.push(upId);
                    this['showTab'+upId](data.data);
                },
                error: function(){
                    this.showAlert('Sorry, but occurred unknown error');
                }
            });
        };

        /**
         * Init tabs control
         */
        this.init = function(){
            this.initControl();
            groupId = $('#groupId').val();
            if (typeof groupId == 'undefined') return;
            var li = $('#groupTabs > li');
            li.on('click', this, function(event){
                if ($(this).hasClass('active') == false) {
                    $(this).siblings().removeClass('active');
                    $(this).addClass('active');
                    event.data.switchTabs($(this).get(0).id);
                }
            });
            var urlHash = document.location.hash;
            if (urlHash.length > 0) {
                var selLi = li.parent().children(urlHash);
                if (selLi.size() !== 1 || tabsId.indexOf(urlHash.substr(1)) === -1) alert('You have proceeded via incorrect url.');
                else selLi.trigger('click');
            }
        };

        /**
         * Show tab info about group
         * @param data
         * @returns {*}
         */
        this.showTabInfo = function(data){
            var tab = {};

            tab.div = this.createTabDiv('Info');
            tab.captions = ['Title', 'Description', 'Publications', 'Users', 'Admins', 'Created'];
            tab.content = $.map(data, function(value) {
                return [value];
            });
            tab.table = document.createElement('table');
            tab.table.className = 'table table-bordered';
            tab.table.appendChild(document.createElement('thead')).appendChild(document.createElement('tr')).parentNode.parentNode.appendChild(document.createElement('tbody')).appendChild(document.createElement('tr'));

            var th = '';
            var ta = [['th', 'thead'], ['td', 'tbody']];
            for (var a=0; a<tab.captions.length; a++) {
                for (var b=0; b<2; b++) {
                    th = document.createElement(ta[b][0]);
                    th.appendChild(document.createTextNode(tab[b==0?'captions':'content'][a]));
                    tab.table.getElementsByTagName(ta[b][1])[0].childNodes[0].appendChild(th);
                }
            }
            tab.div.appendChild(tab.table);
            tabSurface.append(tab.div);
            return data;
        };

        /**
         * Show tab that render all users belong to group
         * @param data
         */
        this.showTabUsers = function(data){
            var div = this.createTabDiv('Users');
            div.innerHTML = 'Users list';
            tabSurface.append(div);
        };

        /**
         * Create parent div needed each tab
         * @param id
         * @returns {Element}
         */
        this.createTabDiv = function(id){
            var div = document.createElement('div');
            div.id = 'tab'+id;
            return div;
        };
    };

    Tabs.prototype = new Control();
    (new Tabs()).init();
});