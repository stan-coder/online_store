$(document).ready(function(){

    var tabs = function(){

        var tabsId = ['sheet', 'info', 'users', 'newUsers'];
        var loadedTabs = [];
        var groupId = 0;
        var hash = null;
        var tabSurface = $('#tabSurface');

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
            var hs = this.getSaltedHash();
            $.ajax({
                url: url,
                data: {
                    groupId: groupId,
                    name: 'Root',
                    hash: hs[0],
                    salt: hs[1]
                },
                success: function(data){
                    if (!data.success) {
                        this.show('There is an error as a result of request');
                        return;
                    }
                    this['showTab'+upId](data.data);
                },
                error: function(){
                    this.show('Sorry, but occurred unknown error');
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
                dataType: 'json',
                context: this
            });
        };

        /**
         * Get array contains salted hash and appropriate salt
         * @returns {*[]}
         */
        this.getSaltedHash = function(){
            var salt = (new Date()).getTime();
            for (var h=0; h<10; h++) {
                salt += Math.random().toString();
            }
            salt = sha512(salt).substr(0, 20);
            return [sha512(hash + salt).substr(0, 50), salt];
        };

        /**
         * Show tab info about group
         * @param json
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

        this.createTabDiv = function(id){
            var div = document.createElement('div');
            div.id = 'tab'+id;
            return div;
        };

        this.showAlert = function(message){
            alert(message);
        }
    };

    (new tabs()).init();
});