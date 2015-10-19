/**
 * This class contains basic methods to perform fundamental actions
 */

function Control(){

    var hash = null;

    /**
     * Init control class
     */
    this.initControl = function(){
        hash = $('#hash').val();
        $.ajaxSetup({
            type: 'POST',
            dataType: 'json',
            context: this,
            beforeSend: function(jqxhr, settings){
                var hs = this.getSaltedHash();
                settings.data += '&hash='+hs[0]+'&salt='+hs[1];
            }
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

    this.showAlert = function(message){
        alert(message);
    }
}

$f = new Control();