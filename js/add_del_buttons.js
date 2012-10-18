(function($){
    $(document).ready(function(){
        $(".addRow-ignoreClass").btnAddRow({maxRow:50,ignoreClass:"noClone"});
        $(".delRow").btnDelRow();
    });
})(jQuery);
