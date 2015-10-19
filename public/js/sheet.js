$(document).ready(function(){

    function Sheet(){

        var json = {};
        var entityType = ['Publication'];

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
            console.log(data);
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
