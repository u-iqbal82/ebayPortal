$(document).ready(function(){
    //select all checkboxes
    $("#select_all").change(function(){  //"select all" change 
        $(".cats").prop('checked', $(this).prop("checked")); //change all ".cats" checked status
    });
    
    //".cats" change 
    $('.cats').change(function(){ 
        //uncheck "select all", if one of the listed checkbox item is unchecked
        if(false == $(this).prop("checked")){ //if this item is unchecked
            $("#select_all").prop('checked', false); //change "select all" checked status to false
        }
        //check "select all" if all checkbox items are checked
        if ($('.cats:checked').length == $('.cats').length ){
            $("#select_all").prop('checked', true);
        }
    });
    
    //select all checkboxes
    $("#select_all_art").change(function(){
        $(".cats_art").prop('checked', $(this).prop("checked"));
    });
    
    $('.cats_art').change(function(){ 

        if(false == $(this).prop("checked"))
        {
            $("#select_all_art").prop('checked', false);
        }
        
        if ($('.cats_art:checked').length == $('.cats_art').length )
        {
            $("#select_all_art").prop('checked', true);
        }
    });
    

    $('#categories').on('click, change', (function(){ 
        var selected = $('#categories').val();
        if (selected == 'all')
        {
            selected = selected.replace("/all", "");
        }
        window.location.href = selected;
    })
    );

});    