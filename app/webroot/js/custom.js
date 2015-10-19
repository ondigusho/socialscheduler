$(document).ready(function(){
	
	$("#username").focus(function() {
		
		$(this).parent(".input-prepend").addClass("input-prepend-focus");
	
	});
	
	$("#username").focusout(function() {
		
		$(this).parent(".input-prepend").removeClass("input-prepend-focus");
	
	});
	
	$("#password").focus(function() {
		
		$(this).parent(".input-prepend").addClass("input-prepend-focus");
	
	});
	
	$("#password").focusout(function() {
		
		$(this).parent(".input-prepend").removeClass("input-prepend-focus");
	
	});
	/* ---------- Add class .active to current link  ---------- */
	$('ul.main-menu li a').each(function(){
		if($($(this))[0].href==String(window.location))
                    	$(this).parent().addClass('active');
	});

        $('textarea').keyup(function () {
            var length = $(this).val().length;
            $('#chars').text(length);
        });
    
	/* ---------- Acivate Functions ---------- */
        template_functions();
	init_masonry();
	charts();
	calendars();
	growlLikeNotifications();
        sendtmz()
        /*checkstatus();*/
        addmoreemails();
        deleteemail();
        editemail();
        add_emails_contact();
        rm_email_view();
        deleteemail_cs();
        deletesocialprofile();
        smtp_sh();
        socialpost();
        editpost();
        new_campaign();
});


function new_campaign(){
    //toggle link div
    $("#ShowInsLinkCamp").click(function () {
        if ( $('#showinsertlink').css('display') == 'none' ){
            // element is hidden
            $('#showinsertlink').show();
            
        }else{
            $('#showinsertlink').fadeOut("slow");
        }
    });
}

function isUrl(s) {
    var regexp = /((([A-Za-z]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+|(?:www.|[-;:&=\+\$,\w]+@)[A-Za-z0-9.-]+)((?:\/[\+~%\/.\w-_]*)?\??(?:[-\+=&;%@.\w_]*)#?(?:[\w]*))?)/
    return regexp.test(s);
}

function loadObject(){
    var app;
        app = app || (function () {
            var process = $('<div id="process" class="modal hide fade" tabindex="-1" data-backdrop="static" data-keyboard="false"><div class="modal-header"><h3>Processing...</h3></div><div class="modal-body"><div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div></div></div>');
            return {
                showProcess: function () {
                    process.modal('show');
                },
                hideProcess: function () {
                    process.modal('hide');
                },
            };
        })();
    //app    
    return app;    
}


function editpost(){
    
    //on submit post
    $("#editsocialpostEditForm").submit(function(e) {
        $('#erroroninput').empty();
        var twitter = 0;
        //check if any twitter 
        $("input:checkbox").each(function () {
            var $this = $(this);
            //if checked
            if ($this.is(":checked")) {
                var id = $this.attr("id");
                var clickedID = id.split("-");
                var type = clickedID[0];
                //split id
                if (type == 'twuseredit') {
                    twitter = 1;
                }
            }
        });
        //check if something checked
        var atLeastOneIsChecked = $("[name='addcontacts[]']:checked").length;
        var msg = $("#textarea2").val();
        if (jQuery.trim(msg).length <= 0){
            $('#erroroninput').show();
            $('#erroroninput').append('<span>Please type a message!</span>');
            return false;
        }
        if ($(atLeastOneIsChecked).length <= 0 ) {
           $('#erroroninput').show();
           $('#erroroninput').append('<span>Please select at least one Social Profile!</span>');
           return false;
        }
        //twitter
        if (twitter > 0) {
            if (jQuery.trim(msg).length > 140) {
                $('#erroroninput').show();
                $('#erroroninput').append('<span>There is a limit of 140 characters on Twitter!</span>');
                return false;
            }
        }
    });
    var dateToday = new Date(); 
    //rescedule link 
    $('#optionsRadiosChange').datetimepicker({
        controlType: 'select',
        oneLine: true,
        dateFormat: "yy-mm-dd",
        timeFormat: 'hh:mm:ss',
        minDate: dateToday,
        onSelect: function (selectedDateTime){
          if ($('#showschedule-edit').html() != "") {
                $('#showschedule-edit').empty();
            }
        $('#showschedule-edit').show();
        $('#showschedule-edit').append('<button class="close" data-dismiss="alert" type="button">×</button><strong>Rescheduled To : </strong><strong id="datetimepost">'+selectedDateTime+'</strong>');
        //rename submit to schedule
        $('#showschedule-edit').append(' <input type="hidden" name="scheduled-edit" id="scheduled-edit" value="'+selectedDateTime+'" />');
    },
//        onClose: function(){
//           alert('closing'); 
//        }
    });
    
    //on remove image
    $("#removeImage").on( "click", function() {
        //get id
        var pid = this.parentNode.id;
        var pid_array = pid.split('-');
        var dbid = pid_array[2];
        
        jQuery.ajax({
            type: "POST", // HTTP method POST or GET
            url:$(this).attr('href'), //Where to make Ajax calls
            dataType:"text", // Data type, HTML, json etc.
            data:{
                recordToDeleteId:dbid,
                typeToDelete:'removepicture'
            },
            success: function (response) {
                //on success, hide element
                $('#' + pid).fadeOut("slow");
                $('#' + pid).empty(); //empty div
            },
            error: function (xhr, ajaxOptions, thrownError) {
                //On error, we alert user
                alert('Error : The operation cannot be complete. You are trying to access data you are not authorized!');
            }
        });
    });
}

function socialpost(){
    //use load animation
    var app = loadObject();
    //on submit post
    $("#socialpostsNewpostForm").submit(function(e) {
        $('#erroroninput').empty();
        var twitter = 0;
        //check if any twitter 
        $("input:checkbox").each(function () {
            var $this = $(this);
            //if checked
            if ($this.is(":checked")) {
                var id = $this.attr("id");
                var clickedID = id.split("-");
                var type = clickedID[0];
                //split id
                if (type == 'twuser') {
                    twitter = 1;
                }
            }
        });
        //check if something checked
        var atLeastOneIsChecked = $("[name='addcontacts[]']:checked").length;
        var msg = $("#textarea2").val();
        if (jQuery.trim(msg).length <= 0){
            $('#erroroninput').show();
            $('#erroroninput').append('<span>Please type a message!</span>');
            return false;
        }
        if ($(atLeastOneIsChecked).length <= 0 ) {
           $('#erroroninput').show();
           $('#erroroninput').append('<span>Please select at least one Social Profile!</span>');
           return false;
        }
        if (twitter > 0) {
            if (jQuery.trim(msg).length > 140) {
                $('#erroroninput').show();
                $('#erroroninput').append('<span>There is a limit of 140 characters on Twitter!</span>');
                return false;
            }
        }
    });
    //on link submit
    $("#linksubmit").on( "click", function() {
        //get value ...validate, error message if not, parse some html , replace all on #showlink
        $('#showlinkposts').empty();
        $('#showlinkposts').hide();
        var url = $('#insertedUrl').val();
        //if a valid url
        if (isUrl(url)){
            //parse website . get name and title 
            // replace with this. div
            //processing 
            $('#showlink').empty();
            $('#inserthidden').empty();
            app.showProcess();
            //start scrap html 
            jQuery.ajax({
                type: "POST", // HTTP method POST or GET
                url: $(this).attr('href'), //Where to make Ajax calls
                dataType: "text", // Data type, HTML, json etc.
                data: {
                    scrap: 'scraphtml',
                    url: url
                },
                success: function (response) {
                    //handle response
                   var obj = jQuery.parseJSON( response );
                   $('#showlink').append('<button class="close" data-dismiss="alert" type="button">×</button><img src="'+obj.images+'" width="80px;" style="padding: 0px 10px 10px 0px;" /> <a href="'+url+'">'+url+'</a> <br><strong>'+ obj.title +'</strong>');
                    //insert url as hidden
                    $('#showlink').append('<input type="hidden" name="urlsubmit" id="urlsubmit" value="'+url+'"/>');
                    app.hideProcess();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    app.hideProcess();
                    //On error, we alert user
                    alert('Error : The operation cannot be complete. You are trying to access data you are not authorized!');
                }
            });
        }else{
            //print error message
            $('#iferror').empty();
            $('#iferror').append('<div class="emerror">Invalid url</div>');
        }
        
    });
    //toggle link div
    $("#insLink").click(function () {
        if ( $('#showlink').css('display') == 'none' ){
            // element is hidden
            $('#showlink').show();
            
        }else{
            $('#showlink').fadeOut("slow");
        }
    });
    //if option one is clicked
    $("#optionsNow").click(function () {
            //remove all
            $('#scheduled').remove();
            $('#showschedule').empty();
            $('#showschedule').fadeOut("slow");
            $('#postsubmit').val('Post');
    });
    var dateToday = new Date(); 
    
    //radio 
    $('#optionsSchedule').datetimepicker({
        controlType: 'select',
        oneLine: true,
        dateFormat: "yy-mm-dd",
        minDate: dateToday,
        onSelect: function (selectedDateTime){
            if ($('#showschedule').html() != "") {
                $('#showschedule').empty();
            }
        $('#showschedule').show();
        $('#showschedule').append('<button class="close" data-dismiss="alert" type="button">×</button><strong>Scheduled For : </strong><strong id="datetimepost">'+selectedDateTime+'</strong>');
        //rename submit to schedule
        $('#postsubmit').val('Schedule');
        $('#showschedule').append(' <input type="hidden" name="scheduled" id="scheduled" value="'+selectedDateTime+'" />');
    },
//        onClose: function(){
//           alert('closing'); 
//        }
    });
    
}

//
//function clean_for_edit(){
//    $('#showschedule-edit').empty();
//    $('#showlink-edit').empty();
//    //clean checked 
//    $('.profile-check').each(function(){
//        alert ('Cleaning'+ this);
//        this.checked = false;
//    });
//    
//}
//


function deletesocialprofile(){
    $("body").on("click",".deletefbuser", function(e){ //user click on remove text
        $(this).parent('div').remove(); //remove text box
        var clickedID = this.id.split("-");
        var type = clickedID[0];
        var dbid = clickedID[1];
        jQuery.ajax({
            type: "POST", // HTTP method POST or GET
            url:$(this).attr('href'), //Where to make Ajax calls
            dataType:"text", // Data type, HTML, json etc.
            data:{
                recordToDeleteId:dbid,
                typeToDelete:type
                         },
            success: function (response) {
                //on success, hide element user wants to delete.
                $('#item_' + dbid).remove();
                $('#item_' + dbid).fadeOut("slow");
                //remove all pages connected to this user id
                //for each page get id
                $('.deletefbpage').each(function () {
                    var pageId = this.id.split("-");
                    //get fb_uid
                    var fb_uid = pageId[2];
                    if (fb_uid === dbid) {
                        //must be removed
                        //remove elements from post table also
                        $('#' + this.id).remove();
                        $('#' + this.id).fadeOut("slow");
                    }
                });
            },
            error: function (xhr, ajaxOptions, thrownError) {
                //On error, we alert user
                alert('Error : The operation cannot be complete. You are trying to access data you are not authorized!');
            }
        });
    });
    //if page remove
    $("body").on("click",".deletefbpage", function(e){ //user click on remove text
        $(this).parent('div').remove(); //remove text box
        var clickedID = this.id.split("-");
        var type = clickedID[0];
        var dbid = clickedID[1];
        var fcb_uid = clickedID[2];
        jQuery.ajax({
            type: "POST", // HTTP method POST or GET
            url:$(this).attr('href'), //Where to make Ajax calls
            dataType:"text", // Data type, HTML, json etc.
            data:{
                recordToDeleteId:dbid,
                typeToDelete:type,
                fcb_uid:fcb_uid
            },
            success: function (response) {
                //on success, hide element user wants to delete.
                $('#fbpage-' + dbid +'-'+fcb_uid).fadeOut("slow");
                $('#fbpage-' + dbid +'-'+fcb_uid).remove();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                //On error, we alert user
                alert('Error : The operation cannot be complete. You are trying to access data you are not authorized!');
            }
        });
    });
    
    //if twitter remove
    $("body").on("click",".deletetwuser", function(e){ //user click on remove text
        $(this).parent('div').remove(); //remove text box
        var clickedID = this.id.split("-");
        var type = clickedID[0];
        var dbid = clickedID[1];
        jQuery.ajax({
            type: "POST", // HTTP method POST or GET
            url:$(this).attr('href'), //Where to make Ajax calls
            dataType:"text", // Data type, HTML, json etc.
            data:{
                recordToDeleteId:dbid,
                typeToDelete:type,
            },
            success: function (response) {
                //on success, hide element user wants to delete.
                $('#twuser-' + dbid ).fadeOut("slow");
                $('#twuser-' + dbid ).remove();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                //On error, we alert user
                alert('Error : The operation cannot be complete. You are trying to access data you are not authorized!');
            }
        });
    });
}

function uploadFileOnClick(id, file, post, _autoUpload) {
        $('#'+id).fileupload({
            dataType: 'json',
            multipart: true,
            autoUpload: true,
            formData: post,
            forceIframeTransport: true,
            progress: function (e, data) {
                /*insert progress code here*/
            },
            done: function (e, data) {
                /*insert your code here*/
            }
        });
        if (_autoUpload) {
            $('#'+id).fileupload('add', {files: file});
        }
}

function smtp_sh(){
    //if it is already checked
    if($('#optionsRadios3').is(':checked')){
        $("#pwdinPut").hide();
    }
    $("#optionsRadios3").click(function() {
        $("#pwdinPut").hide();
    });
    $("#optionsRadios2").click(function() {
        $("#pwdinPut").show();
    });
    $("#optionsRadios1").click(function() {
        $("#pwdinPut").show();
    });
}


function autosave() {
    save_s_b();
}// end autosave
 
//set the autosave interval (60 seconds * 1000 milliseconds per second)
//setInterval(autosave, 6 * 1000);

// Send local time zone 
function sendtmz(){
    $.ajax({
    type: "POST",
    url: "tmz",
    data: 'timezone=' + jstz.determine().name(),
    success: function(data){
    //    location.reload();
    }
});
}

//Make a save for each remove
function save_s_b_on_remove(){
$('.remove').click(function(event){
        //Do save for each of removes
        save_s_b();
        //event.preventDefault();
    });
}

function rm_email_view(){
    $("body").on("click",".removeemailview", function(e){ //user click on remove text
       $(this).parent('div').remove(); //remove text box
       var clickedID = this.id.split("-");
       var dbid = clickedID[1];
       $("#hiddenemail-"+dbid).remove();
    }); 
}

function add_emails_contact(){
    $('#emailsIndexForm').click(function( event ) {
        //var emails = $('input[name=addcontacts[]]', this).val();
        //var fields = $( "#field_1" ).serializeArray();
        for (var i = 1; i<10; i++){
            var field = $('#field_'+i ).val();
            if(field.length){
                //Insert the value
                $("#results").append('<input type="hidden" class="hiddenemail" id="hiddenemail-'+ i +'" name="emaillist[]" value="'+field+'">')
                //console.log(field);
                //jQuery.each( fields, function( i, field ) {
                $( "#results" ).append('<td> <div class="editemailclass" id="email-'+ i +'"> | ' + field + '<a class="removeemailview" href="#" id="email-'+ i +'">×</a></div></td>' );
            }
        //});
        }
       
        //event.preventDefault();
    });
}

//Save subject and body. Will be executed from autosave
// on a 20 sec interval
function save_s_b(){
    //console.log ( '#campaignsEditForm was clicked' );
    //Get all needed data
    var c_name =  $( "input[name='data[MyMarketing][name]']" ).val();
    var subject =  $( "input[name='data[MyMarketing][subject]']" ).val();
    var body =  $( '#textarea2').val();
    var marketing_id = $('#MyMarketingId').val();
    var user_id = $('#MyMarketingUserId').val();
    //Insert as hidden and send for save on server 
    jQuery.ajax({
        type: "POST", // HTTP method POST or GET
        url:$(this).attr('href'), //Where to make Ajax calls
        dataType:"text", // Data type, HTML, json etc.
        data: {c_name:c_name,
               subject:subject,
               body:body,
               marketing_id:marketing_id,
               user_id:user_id
        },
        success:function(response){
                //Something wrong ? 
//                if(response=='success'){
//                }
//                else{
//                    alert(response);
//                }
        },
        error:function (xhr, ajaxOptions, thrownError){
                //On error, we alert user
                //alert(thrownError);
        }
   }); 
        //For testing , prevent from submit
        //event.preventDefault();
}
// Will automatically save body and subject anytime list,email is added or deleted. 
//function save_subject_body(){
//   // for each case run save. 
//   $("#campaignsEditForm").submit(function(event){
//        save_s_b();
//   });
//   $("#clistEditForm").submit(function(event){
//        save_s_b();
//   });
//   $("#emailsEditForm").submit(function(event){
//        save_s_b();
//   });
//}
function addmoreemails(){
    var MaxInputs       = 8; //maximum input boxes allowed
    var InputsWrapper   = $("#InputsWrapper"); //Input boxes wrapper ID
    var AddButton       = $("#AddMoreFileBox"); //Add button ID

    var x = InputsWrapper.length; //initlal text box count
    var FieldCount=1; //to keep track of text box added

    $(AddButton).click(function (e)  //on add input button click
    {
            if(x <= MaxInputs) //max input box allowed
            {
                FieldCount++; //text box added increment
                //add input box
                $(InputsWrapper).append('<div><input type="email" name="addcontacts[]" id="field_'+ FieldCount +'" placeholder="type email '+ FieldCount +'"/><a href="#" class="removeclass">&times;</a></div>');
                x++; //text box increment
            }
    return false;
    });

    $("body").on("click",".removeclass", function(e){ //user click on remove text
            if( x > 1 ) {
                    $(this).parent('div').remove(); //remove text box
                    x--; //decrement textbox
            }
    return false;
    }); 
}
function deleteemail(){
    $("body").on("click",".removeemailclass", function(e){ //user click on remove text
                    $(this).parent('div').remove(); //remove text box
                    var clickedID = this.id.split("-");
                    var dbid = clickedID[1];
                    var myData = 'recordToDelete='+ dbid;
                    jQuery.ajax({
                        type: "POST", // HTTP method POST or GET
                        url:$(this).attr('href'), //Where to make Ajax calls
                        dataType:"text", // Data type, HTML, json etc.
                        data:myData, //post variables
                        success:function(response){
                            //on success, hide element user wants to delete.
                            $('#item_'+dbid).fadeOut("slow");
                        },
                        error:function (xhr, ajaxOptions, thrownError){
                            //On error, we alert user
                            alert(thrownError);
                        }
                    });
    }); 
}

function deleteemail_cs(){
    $("body").on("click",".rmemailcs", function(e){ //user click on remove text
                    $(this).parent('div').remove(); //remove text box
                    var clickedID = this.id.split("-");
                    var dbid = clickedID[1];
                    var myData = 'recordToDelete='+ dbid;
                    jQuery.ajax({
                        type: "POST", // HTTP method POST or GET
                        url:$(this).attr('href'), //Where to make Ajax calls
                        dataType:"text", // Data type, HTML, json etc.
                        data:myData, //post variables
                        success:function(response){
                            //on success, hide element user wants to delete.
                            $('#item_'+dbid).fadeOut("slow");
                        },
                        error:function (xhr, ajaxOptions, thrownError){
                            //On error, we alert user
                            alert('Error : The operation cannot be complete. You are trying to access data you are not authorized!');
                        }
                    });
    }); 
}

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(emailAddress);
};

function editemail(){
    $("body").on("click",".editemailclass", function(e){ //user click on remove text
        var text = $(this).text();
        $(this).text('');
        var clickedID = this.id.split("-");
        var dbid = clickedID[1];
        $('<input type="email" name="email" id="field_" value ="'+text+'"/>').appendTo($(this)).val(text).select().blur(
            function(){
                $('#iferror-'+dbid).empty();
                var newText = $(this).val();
                if (isValidEmailAddress(newText)){
                   $(this).parent().text(newText).find('textarea').remove();
                    var myData = []; 
                    myData[0]= dbid; 
                    myData[1] = newText;
                    jQuery.ajax({
                            type: "POST", // HTTP method POST or GET
                            url:$(this).attr('href'), //Where to make Ajax calls
                            dataType:"text", // Data type, HTML, json etc.
                            data: {info:myData},
                            success:function(response){
                                //on success, hide element user wants to delete.
                                //$('#item_'+dbid).fadeOut("slow");
                            },
                            error:function (xhr, ajaxOptions, thrownError){
                                //On error, we alert user
                                alert(thrownError);
                      }
                    }); 
                }//end if email address
                else{
                    $('#iferror-'+dbid).append('<div class="emerror">Insert valid email</div>');
                }
            });
        
    }); 
}

/*
function checkstatus(){
    setInterval(function() {
        $.ajax({
        url: 'http://localhost/admin/main/status',
        data: "", dataType: 'json',
        success: function(rows) {
        for (var i in rows) {
            var row = rows[i];
                 
                $("#DataTables_Table_0 td").each(function () {
                for (var i = 0; i < $(this).children.length; i++) {
                    $('.label-warning').html('Whatever <b>HTML</b> you want here.' + row + $(this).children(i).val());
                }
            });
          }
        }
        });
    }, 5000);
}*/

/* ---------- Masonry Gallery ---------- */

function init_masonry(){
    var $container = $('.masonry-gallery');

    var gutter = 6;
    var min_width = 250;
    $container.imagesLoaded( function(){
        $container.masonry({
            itemSelector : '.masonry-thumb',
            gutterWidth: gutter,
            isAnimated: true,
              columnWidth: function( containerWidth ) {
                var num_of_boxes = (containerWidth/min_width | 0);

                var box_width = (((containerWidth - (num_of_boxes-1)*gutter)/num_of_boxes) | 0) ;

                if (containerWidth < min_width) {
                    box_width = containerWidth;
                }

                $('.masonry-thumb').width(box_width);

                return box_width;
              }
        });
    });
}

/* ---------- Numbers Sepparator ---------- */

function numberWithCommas(x) {
    x = x.toString();
    var pattern = /(-?\d+)(\d{3})/;
    while (pattern.test(x))
        x = x.replace(pattern, "$1.$2");
    return x;
}

/* ---------- Dynamic Couting on Start Page ---------- */
			
function f_visits() {
	
	var base = 1998746;
	
	var base100 = (base * 100) / 155;
			
	var visits = parseInt($("#visits-count").html().replace(/\./g,""));
			
	var random = Math.floor((Math.random()*10)+1);
	
	var visits_n = visits + random;
	
	$("#visits-count-n").html("+ " + Math.round(((visits_n/base100) - 1) * 100) + "%");
			
	$("#visits-count").html(numberWithCommas(visits_n));
	
}


function f_members() {
	
	var base = 794278;
	
	var members = parseInt($("#members-count").html().replace(/\./g,""));
			
	var random = Math.floor((Math.random()*10)+1);
	
	var members_n = members + random;
	
	$("#members-count-n").html("+ " + numberWithCommas(1586 + (members_n - base)));
			
	$("#members-count").html(numberWithCommas(members_n));
	
}


function f_income() {
	
	var base = 519879;
	
	var income = parseInt(($("#income-count").html().replace("$","")).replace(/\./g,""));
			
	var random = Math.floor((Math.random()*1324)+1);
	
	var income_n = income + random;
	
	$("#income-count-n").html("+ $" + numberWithCommas(29875 + (income_n - base)));
			
	$("#income-count").html("$" + numberWithCommas(income_n));
	
}


function f_sales() {
	
	var base = 11976;
	
	var sales = parseInt($("#sales-count").html().replace(/\./g,""));
			
	var random = Math.floor((Math.random()*10)+1);
	
	var sales_n = sales + random;
	
	$("#sales-count-n").html("+ " + numberWithCommas(1586 + (sales_n - base)));
			
	$("#sales-count").html(numberWithCommas(sales_n));
	
}

/* ---------- Notification Center - Cooming Soon in next version ---------- */

function live_notifications_center(){
	
	$('<div class="item"><img class="dashboard-avatar" alt="Lucas" src="img/avatar.jpg"><h4>Action1</h4><p>description1</p></div>')
	.prependTo('#notifications-center')
	.hide()
	.slideDown('slow')
	.css('opacity', 0)
	.animate(
	    { opacity: 1 },
	    { duration: 'slow' }
	  );
	
} 

/* ---------- Template Functions ---------- */		
		
function template_functions(){
	
	/* ---------- Skill Bars ---------- */
	$(".meter > span").each(function() {
		$(this)
		.data("origWidth", $(this).width())
		.width(0)
		.animate({
			width: $(this).data("origWidth")
		}, 3000);
	});
	
	/* ---------- Disable moving to top ---------- */
	$('a[href="#"][data-top!=true]').click(function(e){
		e.preventDefault();
	});
	
	/* ---------- Text editor ---------- */
	$('.cleditor').cleditor({
            width:        655, // width not including margins, borders or padding
            height:       450, // height not including margins, borders or padding
        });
	
	/* ---------- Datapicker ---------- */
	$('.datepicker').datepicker();
	
	/* ---------- Notifications ---------- */
	$('.noty').click(function(e){
		e.preventDefault();
		var options = $.parseJSON($(this).attr('data-noty-options'));
		noty(options);
	});

	/* ---------- Uniform ---------- */
	$("input:checkbox, input:radio, input:file").not('[data-no-uniform="true"],#uniform-is-ajax').uniform();

	/* ---------- Choosen ---------- */
	$('[data-rel="chosen"],[rel="chosen"]').chosen();

	/* ---------- Tabs ---------- */
	$('#myTab a:first').tab('show');
	$('#myTab a').click(function (e) {
	  e.preventDefault();
	  $(this).tab('show');
	});

	/* ---------- Makes elements soratble, elements that sort need to have id attribute to save the result ---------- */
	$('.sortable').sortable({
		revert:true,
		cancel:'.btn,.box-content,.nav-header',
		update:function(event,ui){
			//line below gives the ids of elements, you can make ajax call here to save it to the database
			//console.log($(this).sortable('toArray'));
		}
	});

	/* ---------- Tooltip ---------- */
	$('[rel="tooltip"],[data-rel="tooltip"]').tooltip({"placement":"bottom",delay: { show: 400, hide: 200 }});

	/* ---------- Popover ---------- */
	$('[rel="popover"],[data-rel="popover"]').popover();

	/* ---------- File Manager ---------- */
	var elf = $('.file-manager').elfinder({
		url : 'misc/elfinder-connector/connector.php'  // connector URL (REQUIRED)
	}).elfinder('instance');

	/* ---------- Star Rating ---------- */
	$('.raty').raty({
		score : 4 //default stars
	});

	/* ---------- Uploadify ---------- */
	$('#file_upload').uploadify({
		'swf'      : 'misc/uploadify.swf',
		'uploader' : 'misc/uploadify.php'
		// Put your options here
	});

	/* ---------- Fullscreen ---------- */
	$('#toggle-fullscreen').button().click(function () {
		var button = $(this), root = document.documentElement;
		if (!button.hasClass('active')) {
			$('#thumbnails').addClass('modal-fullscreen');
			if (root.webkitRequestFullScreen) {
				root.webkitRequestFullScreen(
					window.Element.ALLOW_KEYBOARD_INPUT
				);
			} else if (root.mozRequestFullScreen) {
				root.mozRequestFullScreen();
			}
		} else {
			$('#thumbnails').removeClass('modal-fullscreen');
			(document.webkitCancelFullScreen ||
				document.mozCancelFullScreen ||
				$.noop).apply(document);
		}
	});

	/* ---------- Datable ---------- */
	$('.datatable').dataTable({
			"sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span12'i><'span12 center'p>>",
			"sPaginationType": "bootstrap",
			"oLanguage": {
			"sLengthMenu": "_MENU_ records per page"
			}
                        
          	} );
	$('.btn-close').click(function(e){
		e.preventDefault();
		$(this).parent().parent().parent().fadeOut();
	});
	$('.btn-minimize').click(function(e){
		e.preventDefault();
		var $target = $(this).parent().parent().next('.box-content');
		if($target.is(':visible')) $('i',$(this)).removeClass('icon-chevron-up').addClass('icon-chevron-down');
		else 					   $('i',$(this)).removeClass('icon-chevron-down').addClass('icon-chevron-up');
		$target.slideToggle();
	});
	$('.btn-setting').click(function(e){
		e.preventDefault();
		$('#myModal').modal('show');
	});
	
        $('.btn-setting-cs').click(function(e){
		e.preventDefault();
		$('#myModal-cs').modal('show');
	});
	
         $('.btn-setting-mc').click(function(e){
		e.preventDefault();
		$('#myModal-mc').modal('show');
	});
	/* ---------- Progress  ---------- */

		$(".simpleProgress").progressbar({
			value: 89
		});

		$(".progressAnimate").progressbar({
			value: 1,
			create: function() {
				$(".progressAnimate .ui-progressbar-value").animate({"width":"100%"},{
					duration: 10000,
					step: function(now){
						$(".progressAnimateValue").html(parseInt(now)+"%");
					},
					easing: "linear"
				})
			}
		});

		$(".progressUploadAnimate").progressbar({
			value: 1,
			create: function() {
				$(".progressUploadAnimate .ui-progressbar-value").animate({"width":"100%"},{
					duration: 20000,
					easing: 'linear',
					step: function(now){
						$(".progressUploadAnimateValue").html(parseInt(now*40.96)+" Gb");
					},
					complete: function(){
						$(".progressUploadAnimate + .field_notice").html("<span class='must'>Upload Finished</span>");
					} 
				})
			}
		});
	
	
	/* ---------- Custom Slider ---------- */
		$(".sliderSimple").slider();

		$(".sliderMin").slider({
			range: "min",
			value: 180,
			min: 1,
			max: 700,
			slide: function( event, ui ) {
				$( ".sliderMinLabel" ).html( "$" + ui.value );
			}
		});

		$(".sliderMin-1").slider({
			range: "min",
			value: 50,
			min: 1,
			max: 700,
			slide: function( event, ui ) {
				$( ".sliderMin1Label" ).html( "$" + ui.value );
			}
		});

		$(".sliderMin-2").slider({
			range: "min",
			value: 100,
			min: 1,
			max: 700,
			slide: function( event, ui ) {
				$( ".sliderMin2Label" ).html( "$" + ui.value );
			}
		});

		$(".sliderMin-3").slider({
			range: "min",
			value: 150,
			min: 1,
			max: 700,
			slide: function( event, ui ) {
				$( ".sliderMin3Label" ).html( "$" + ui.value );
			}
		});

		$(".sliderMin-4").slider({
			range: "min",
			value: 250,
			min: 1,
			max: 700,
			slide: function( event, ui ) {
				$( ".sliderMin4Label" ).html( "$" + ui.value );
			}
		});

		$(".sliderMin-5").slider({
			range: "min",
			value: 350,
			min: 1,
			max: 700,
			slide: function( event, ui ) {
				$( ".sliderLabel" ).html( "$" + ui.value );
			}
		});
		
		$(".sliderMin-6").slider({
			range: "min",
			value: 450,
			min: 1,
			max: 700,
			slide: function( event, ui ) {
				$( ".sliderLabel" ).html( "$" + ui.value );
			}
		});
		
		$(".sliderMin-7").slider({
			range: "min",
			value: 550,
			min: 1,
			max: 700,
			slide: function( event, ui ) {
				$( ".sliderLabel" ).html( "$" + ui.value );
			}
		});
		
		$(".sliderMin-8").slider({
			range: "min",
			value: 650,
			min: 1,
			max: 700,
			slide: function( event, ui ) {
				$( ".sliderLabel" ).html( "$" + ui.value );
			}
		});
		
		
		$(".sliderMax").slider({
			range: "max",
			value: 280,
			min: 1,
			max: 700,
			slide: function( event, ui ) {
				$( ".sliderMaxLabel" ).html( "$" + ui.value );
			}
		});

		$( ".sliderRange" ).slider({
			range: true,
			min: 0,
			max: 500,
			values: [ 192, 470 ],
			slide: function( event, ui ) {
				$( ".sliderRangeLabel" ).html( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
			}
		});

		$( "#sliderVertical-1" ).slider({
			orientation: "vertical",
			range: "min",
			min: 0,
			max: 100,
			value: 60,
		});

		$( "#sliderVertical-2" ).slider({
			orientation: "vertical",
			range: "min",
			min: 0,
			max: 100,
			value: 40,
		});

		$( "#sliderVertical-3" ).slider({
			orientation: "vertical",
			range: "min",
			min: 0,
			max: 100,
			value: 30,
		});

		$( "#sliderVertical-4" ).slider({
			orientation: "vertical",
			range: "min",
			min: 0,
			max: 100,
			value: 15,
		});

		$( "#sliderVertical-5" ).slider({
			orientation: "vertical",
			range: "min",
			min: 0,
			max: 100,
			value: 40,
		});

		$( "#sliderVertical-6" ).slider({
			orientation: "vertical",
			range: "min",
			min: 0,
			max: 100,
			value: 80,
		});
		
		$( "#sliderVertical-7" ).slider({
			orientation: "vertical",
			range: "min",
			min: 0,
			max: 100,
			value: 60,
		});

		$( "#sliderVertical-8" ).slider({
			orientation: "vertical",
			range: "min",
			min: 0,
			max: 100,
			value: 40,
		});

		$( "#sliderVertical-9" ).slider({
			orientation: "vertical",
			range: "min",
			min: 0,
			max: 100,
			value: 30,
		});

		$( "#sliderVertical-10" ).slider({
			orientation: "vertical",
			range: "min",
			min: 0,
			max: 100,
			value: 15,
		});

		$( "#sliderVertical-11" ).slider({
			orientation: "vertical",
			range: "min",
			min: 0,
			max: 100,
			value: 40,
		});

		$( "#sliderVertical-12" ).slider({
			orientation: "vertical",
			range: "min",
			min: 0,
			max: 100,
			value: 80,
		});
	
}

/* ---------- Calendars ---------- */

function calendars(){
	

	$('#external-events div.external-event').each(function() {

		// it doesn't need to have a start or end
		var eventObject = {
			title: $.trim($(this).text()) // use the element's text as the event title
		};
		
		// store the Event Object in the DOM element so we can get to it later
		$(this).data('eventObject', eventObject);
		
		// make the event draggable using jQuery UI
		$(this).draggable({
			zIndex: 999,
			revert: true,      // will cause the event to go back to its
			revertDuration: 0  //  original position after the drag
		});
		
	});
	
	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();

	$('#main_calendar').fullCalendar({
		header: {
			left: 'title',
			right: 'prev,next today,month,agendaWeek,agendaDay'
		},
		editable: true,
		events: [
			{
				title: 'All Day Event',
				start: new Date(y, m, 1)
			},
			{
				title: 'Long Event',
				start: new Date(y, m, d-5),
				end: new Date(y, m, d-2)
			},
			{
				id: 999,
				title: 'Repeating Event',
				start: new Date(y, m, d-3, 16, 0),
				allDay: false
			},
			{
				id: 999,
				title: 'Repeating Event',
				start: new Date(y, m, d+4, 16, 0),
				allDay: false
			},
			{
				title: 'Meeting',
				start: new Date(y, m, d, 10, 30),
				allDay: false
			},
			{
				title: 'Lunch',
				start: new Date(y, m, d, 12, 0),
				end: new Date(y, m, d, 14, 0),
				allDay: false
			},
			{
				title: 'Birthday Party',
				start: new Date(y, m, d+1, 19, 0),
				end: new Date(y, m, d+1, 22, 30),
				allDay: false
			},
			{
				title: 'Click for Google',
				start: new Date(y, m, 28),
				end: new Date(y, m, 29),
				url: 'http://google.com/'
			}
		]
	});
	
	$('#main_calendar_phone').fullCalendar({
		header: {
			left: 'title',
			right: 'prev,next today,month,agendaWeek,agendaDay'
		},
		defaultView: 'agendaDay',
		editable: true,
		events: [
			{
				title: 'All Day Event',
				start: new Date(y, m, 1)
			},
			{
				title: 'Long Event',
				start: new Date(y, m, d-5),
				end: new Date(y, m, d-2)
			},
			{
				id: 999,
				title: 'Repeating Event',
				start: new Date(y, m, d-3, 16, 0),
				allDay: false
			},
			{
				id: 999,
				title: 'Repeating Event',
				start: new Date(y, m, d+4, 16, 0),
				allDay: false
			},
			{
				title: 'Meeting',
				start: new Date(y, m, d, 10, 30),
				allDay: false
			},
			{
				title: 'Lunch',
				start: new Date(y, m, d, 12, 0),
				end: new Date(y, m, d, 14, 0),
				allDay: false
			},
			{
				title: 'Birthday Party',
				start: new Date(y, m, d+1, 19, 0),
				end: new Date(y, m, d+1, 22, 30),
				allDay: false
			},
			{
				title: 'Click for Google',
				start: new Date(y, m, 28),
				end: new Date(y, m, 29),
				url: 'http://google.com/'
			}
		]
	});		
	
			
	$('#calendar').fullCalendar({
		header: {
			left: 'title',
			right: 'prev,next today,month,agendaWeek,agendaDay'
		},
		editable: true,
		droppable: true, // this allows things to be dropped onto the calendar !!!
		drop: function(date, allDay) { // this function is called when something is dropped
		
			// retrieve the dropped element's stored Event Object
			var originalEventObject = $(this).data('eventObject');
			
			// we need to copy it, so that multiple events don't have a reference to the same object
			var copiedEventObject = $.extend({}, originalEventObject);
			
			// assign it the date that was reported
			copiedEventObject.start = date;
			copiedEventObject.allDay = allDay;
			
			// render the event on the calendar
			// the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
			$('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
			
			// is the "remove after drop" checkbox checked?
			if ($('#drop-remove').is(':checked')) {
				// if so, remove the element from the "Draggable Events" list
				$(this).remove();
			}
			
		}
	});
	
}

/* ---------- Charts ---------- */

function charts() {
	
	/* ---------- Chart with points ---------- */
	if($("#sincos").length)
	{
		var sin = [], cos = [];

		for (var i = 0; i < 14; i += 0.5) {
			sin.push([i, Math.sin(i)/i]);
			cos.push([i, Math.cos(i)]);
		}

		var plot = $.plot($("#sincos"),
			   [ { data: sin, label: "sin(x)/x"}, { data: cos, label: "cos(x)" } ], {
				   series: {
					   lines: { show: true,
								lineWidth: 2,
							 },
					   points: { show: true },
					   shadowSize: 2
				   },
				   grid: { hoverable: true, 
						   clickable: true, 
						   tickColor: "#dddddd",
						   borderWidth: 0 
						 },
				   yaxis: { min: -1.2, max: 1.2 },
				   colors: ["#FA5833", "#2FABE9"]
				 });

		function showTooltip(x, y, contents) {
			$('<div id="tooltip">' + contents + '</div>').css( {
				position: 'absolute',
				display: 'none',
				top: y + 5,
				left: x + 5,
				border: '1px solid #fdd',
				padding: '2px',
				'background-color': '#dfeffc',
				opacity: 0.80
			}).appendTo("body").fadeIn(200);
		}

		var previousPoint = null;
		$("#sincos").bind("plothover", function (event, pos, item) {
			$("#x").text(pos.x.toFixed(2));
			$("#y").text(pos.y.toFixed(2));

				if (item) {
					if (previousPoint != item.dataIndex) {
						previousPoint = item.dataIndex;

						$("#tooltip").remove();
						var x = item.datapoint[0].toFixed(2),
							y = item.datapoint[1].toFixed(2);

						showTooltip(item.pageX, item.pageY,
									item.series.label + " of " + x + " = " + y);
					}
				}
				else {
					$("#tooltip").remove();
					previousPoint = null;
				}
		});
		


		$("#sincos").bind("plotclick", function (event, pos, item) {
			if (item) {
				$("#clickdata").text("You clicked point " + item.dataIndex + " in " + item.series.label + ".");
				plot.highlight(item.series, item.datapoint);
			}
		});
	}
	
	/* ---------- Flot chart ---------- */
	if($("#flotchart").length)
	{
		var d1 = [];
		for (var i = 0; i < Math.PI * 2; i += 0.25)
			d1.push([i, Math.sin(i)]);
		
		var d2 = [];
		for (var i = 0; i < Math.PI * 2; i += 0.25)
			d2.push([i, Math.cos(i)]);

		var d3 = [];
		for (var i = 0; i < Math.PI * 2; i += 0.1)
			d3.push([i, Math.tan(i)]);
		
		$.plot($("#flotchart"), [
			{ label: "sin(x)",  data: d1},
			{ label: "cos(x)",  data: d2},
			{ label: "tan(x)",  data: d3}
		], {
			series: {
				lines: { show: true },
				points: { show: true }
			},
			xaxis: {
				ticks: [0, [Math.PI/2, "\u03c0/2"], [Math.PI, "\u03c0"], [Math.PI * 3/2, "3\u03c0/2"], [Math.PI * 2, "2\u03c0"]]
			},
			yaxis: {
				ticks: 10,
				min: -2,
				max: 2
			},
			grid: {	tickColor: "#dddddd",
					borderWidth: 0 
			},
			colors: ["#FA5833", "#2FABE9", "#FABB3D"]
		});
	}
	
	/* ---------- Stack chart ---------- */
	if($("#stackchart").length)
	{
		var d1 = [];
		for (var i = 0; i <= 10; i += 1)
		d1.push([i, parseInt(Math.random() * 30)]);

		var d2 = [];
		for (var i = 0; i <= 10; i += 1)
			d2.push([i, parseInt(Math.random() * 30)]);

		var d3 = [];
		for (var i = 0; i <= 10; i += 1)
			d3.push([i, parseInt(Math.random() * 30)]);

		var stack = 0, bars = true, lines = false, steps = false;

		function plotWithOptions() {
			$.plot($("#stackchart"), [ d1, d2, d3 ], {
				series: {
					stack: stack,
					lines: { show: lines, fill: true, steps: steps },
					bars: { show: bars, barWidth: 0.6 },
				},
				colors: ["#FA5833", "#2FABE9", "#FABB3D"]
			});
		}

		plotWithOptions();

		$(".stackControls input").click(function (e) {
			e.preventDefault();
			stack = $(this).val() == "With stacking" ? true : null;
			plotWithOptions();
		});
		$(".graphControls input").click(function (e) {
			e.preventDefault();
			bars = $(this).val().indexOf("Bars") != -1;
			lines = $(this).val().indexOf("Lines") != -1;
			steps = $(this).val().indexOf("steps") != -1;
			plotWithOptions();
		});
	}

	/* ---------- Pie chart ---------- */
	var data = [
	{ label: "Internet Explorer",  data: 12},
	{ label: "Mobile",  data: 27},
	{ label: "Safari",  data: 85},
	{ label: "Opera",  data: 64},
	{ label: "Firefox",  data: 90},
	{ label: "Chrome",  data: 112}
	];
	
	if($("#piechart").length)
	{
		$.plot($("#piechart"), data,
		{
			series: {
					pie: {
							show: true
					}
			},
			grid: {
					hoverable: true,
					clickable: true
			},
			legend: {
				show: false
			},
			colors: ["#FA5833", "#2FABE9", "#FABB3D", "#78CD51"]
		});
		
		function pieHover(event, pos, obj)
		{
			if (!obj)
					return;
			percent = parseFloat(obj.series.percent).toFixed(2);
			$("#hover").html('<span style="font-weight: bold; color: '+obj.series.color+'">'+obj.series.label+' ('+percent+'%)</span>');
		}
		$("#piechart").bind("plothover", pieHover);
	}
	
	/* ---------- Donut chart ---------- */
	if($("#donutchart").length)
	{
		$.plot($("#donutchart"), data,
		{
				series: {
						pie: {
								innerRadius: 0.5,
								show: true
						}
				},
				legend: {
					show: false
				},
				colors: ["#FA5833", "#2FABE9", "#FABB3D", "#78CD51"]
		});
	}




	 // we use an inline data source in the example, usually data would
	// be fetched from a server
	var data = [], totalPoints = 300;
	function getRandomData() {
		if (data.length > 0)
			data = data.slice(1);

		// do a random walk
		while (data.length < totalPoints) {
			var prev = data.length > 0 ? data[data.length - 1] : 50;
			var y = prev + Math.random() * 10 - 5;
			if (y < 0)
				y = 0;
			if (y > 100)
				y = 100;
			data.push(y);
		}

		// zip the generated y values with the x values
		var res = [];
		for (var i = 0; i < data.length; ++i)
			res.push([i, data[i]])
		return res;
	}

	// setup control widget
	var updateInterval = 30;
	$("#updateInterval").val(updateInterval).change(function () {
		var v = $(this).val();
		if (v && !isNaN(+v)) {
			updateInterval = +v;
			if (updateInterval < 1)
				updateInterval = 1;
			if (updateInterval > 2000)
				updateInterval = 2000;
			$(this).val("" + updateInterval);
		}
	});

	/* ---------- Realtime chart ---------- */
	if($("#realtimechart").length)
	{
		var options = {
			series: { shadowSize: 1 },
			lines: { fill: true, fillColor: { colors: [ { opacity: 0.6 }, { opacity: 0.1 } ] }},
			yaxis: { min: 0, max: 100 },
			xaxis: { show: false },
			colors: ["#F4A506"],
			grid: {	tickColor: "#dddddd",
					borderWidth: 0 
			},
		};
		var plot = $.plot($("#realtimechart"), [ getRandomData() ], options);
		function update() {
			plot.setData([ getRandomData() ]);
			// since the axes don't change, we don't need to call plot.setupGrid()
			plot.draw();
			
			setTimeout(update, updateInterval);
		}

		update();
	}
}

function growlLikeNotifications() {
	
	$('#add-sticky').click(function(){

		var unique_id = $.gritter.add({
			// (string | mandatory) the heading of the notification
			title: 'This is a sticky notice!',
			// (string | mandatory) the text inside the notification
			text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus eget tincidunt velit. Cum sociis natoque penatibus et <a href="#" style="color:#ccc">magnis dis parturient</a> montes, nascetur ridiculus mus.',
			// (string | optional) the image to display on the left
			image: 'img/avatar.jpg',
			// (bool | optional) if you want it to fade out on its own or just sit there
			sticky: true,
			// (int | optional) the time you want it to be alive for before fading out
			time: '',
			// (string | optional) the class name you want to apply to that specific message
			class_name: 'my-sticky-class'
		});

		// You can have it return a unique id, this can be used to manually remove it later using
		/* ----------
		setTimeout(function(){

			$.gritter.remove(unique_id, {
				fade: true,
				speed: 'slow'
			});

		}, 6000)
		*/

		return false;

	});

	$('#add-regular').click(function(){

		$.gritter.add({
			// (string | mandatory) the heading of the notification
			title: 'This is a regular notice!',
			// (string | mandatory) the text inside the notification
			text: 'This will fade out after a certain amount of time. Vivamus eget tincidunt velit. Cum sociis natoque penatibus et <a href="#" style="color:#ccc">magnis dis parturient</a> montes, nascetur ridiculus mus.',
			// (string | optional) the image to display on the left
			image: 'img/avatar.jpg',
			// (bool | optional) if you want it to fade out on its own or just sit there
			sticky: false,
			// (int | optional) the time you want it to be alive for before fading out
			time: ''
		});

		return false;

	});

    $('#add-max').click(function(){

        $.gritter.add({
            // (string | mandatory) the heading of the notification
            title: 'This is a notice with a max of 3 on screen at one time!',
            // (string | mandatory) the text inside the notification
            text: 'This will fade out after a certain amount of time. Vivamus eget tincidunt velit. Cum sociis natoque penatibus et <a href="#" style="color:#ccc">magnis dis parturient</a> montes, nascetur ridiculus mus.',
            // (string | optional) the image to display on the left
            image: 'img/avatar.jpg',
            // (bool | optional) if you want it to fade out on its own or just sit there
            sticky: false,
            // (function) before the gritter notice is opened
            before_open: function(){
                if($('.gritter-item-wrapper').length == 3)
                {
                    // Returning false prevents a new gritter from opening
                    return false;
                }
            }
        });

        return false;

    });

	$('#add-without-image').click(function(){

		$.gritter.add({
			// (string | mandatory) the heading of the notification
			title: 'This is a notice without an image!',
			// (string | mandatory) the text inside the notification
			text: 'This will fade out after a certain amount of time. Vivamus eget tincidunt velit. Cum sociis natoque penatibus et <a href="#" style="color:#ccc">magnis dis parturient</a> montes, nascetur ridiculus mus.'
		});

		return false;
	});

    $('#add-gritter-light').click(function(){

        $.gritter.add({
            // (string | mandatory) the heading of the notification
            title: 'This is a light notification',
            // (string | mandatory) the text inside the notification
            text: 'Just add a "gritter-light" class_name to your $.gritter.add or globally to $.gritter.options.class_name',
            class_name: 'gritter-light'
        });

        return false;
    });

	$('#add-with-callbacks').click(function(){

		$.gritter.add({
			// (string | mandatory) the heading of the notification
			title: 'This is a notice with callbacks!',
			// (string | mandatory) the text inside the notification
			text: 'The callback is...',
			// (function | optional) function called before it opens
			before_open: function(){
				alert('I am called before it opens');
			},
			// (function | optional) function called after it opens
			after_open: function(e){
				alert("I am called after it opens: \nI am passed the jQuery object for the created Gritter element...\n" + e);
			},
			// (function | optional) function called before it closes
			before_close: function(e, manual_close){
                var manually = (manual_close) ? 'The "X" was clicked to close me!' : '';
				alert("I am called before it closes: I am passed the jQuery object for the Gritter element... \n" + manually);
			},
			// (function | optional) function called after it closes
			after_close: function(e, manual_close){
                var manually = (manual_close) ? 'The "X" was clicked to close me!' : '';
				alert('I am called after it closes. ' + manually);
			}
		});

		return false;
	});

	$('#add-sticky-with-callbacks').click(function(){

		$.gritter.add({
			// (string | mandatory) the heading of the notification
			title: 'This is a sticky notice with callbacks!',
			// (string | mandatory) the text inside the notification
			text: 'Sticky sticky notice.. sticky sticky notice...',
			// Stickeh!
			sticky: true,
			// (function | optional) function called before it opens
			before_open: function(){
				alert('I am a sticky called before it opens');
			},
			// (function | optional) function called after it opens
			after_open: function(e){
				alert("I am a sticky called after it opens: \nI am passed the jQuery object for the created Gritter element...\n" + e);
			},
			// (function | optional) function called before it closes
			before_close: function(e){
				alert("I am a sticky called before it closes: I am passed the jQuery object for the Gritter element... \n" + e);
			},
			// (function | optional) function called after it closes
			after_close: function(){
				alert('I am a sticky called after it closes');
			}
		});

		return false;

	});

	$("#remove-all").click(function(){

		$.gritter.removeAll();
		return false;

	});

	$("#remove-all-with-callbacks").click(function(){

		$.gritter.removeAll({
			before_close: function(e){
				alert("I am called before all notifications are closed.  I am passed the jQuery object containing all  of Gritter notifications.\n" + e);
			},
			after_close: function(){
				alert('I am called after everything has been closed.');
			}
		});
		return false;

	});


}


/* ---------- Additional functions for data table ---------- */
$.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings )
{
	return {
		"iStart":         oSettings._iDisplayStart,
		"iEnd":           oSettings.fnDisplayEnd(),
		"iLength":        oSettings._iDisplayLength,
		"iTotal":         oSettings.fnRecordsTotal(),
		"iFilteredTotal": oSettings.fnRecordsDisplay(),
		"iPage":          Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
		"iTotalPages":    Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
	};
}
$.extend( $.fn.dataTableExt.oPagination, {
	"bootstrap": {
		"fnInit": function( oSettings, nPaging, fnDraw ) {
			var oLang = oSettings.oLanguage.oPaginate;
			var fnClickHandler = function ( e ) {
				e.preventDefault();
				if ( oSettings.oApi._fnPageChange(oSettings, e.data.action) ) {
					fnDraw( oSettings );
				}
			};

			$(nPaging).addClass('pagination').append(
				'<ul>'+
					'<li class="prev disabled"><a href="#">&larr; '+oLang.sPrevious+'</a></li>'+
					'<li class="next disabled"><a href="#">'+oLang.sNext+' &rarr; </a></li>'+
				'</ul>'
			);
			var els = $('a', nPaging);
			$(els[0]).bind( 'click.DT', { action: "previous" }, fnClickHandler );
			$(els[1]).bind( 'click.DT', { action: "next" }, fnClickHandler );
		},

		"fnUpdate": function ( oSettings, fnDraw ) {
			var iListLength = 5;
			var oPaging = oSettings.oInstance.fnPagingInfo();
			var an = oSettings.aanFeatures.p;
			var i, j, sClass, iStart, iEnd, iHalf=Math.floor(iListLength/2);

			if ( oPaging.iTotalPages < iListLength) {
				iStart = 1;
				iEnd = oPaging.iTotalPages;
			}
			else if ( oPaging.iPage <= iHalf ) {
				iStart = 1;
				iEnd = iListLength;
			} else if ( oPaging.iPage >= (oPaging.iTotalPages-iHalf) ) {
				iStart = oPaging.iTotalPages - iListLength + 1;
				iEnd = oPaging.iTotalPages;
			} else {
				iStart = oPaging.iPage - iHalf + 1;
				iEnd = iStart + iListLength - 1;
			}

			for ( i=0, iLen=an.length ; i<iLen ; i++ ) {
				// remove the middle elements
				$('li:gt(0)', an[i]).filter(':not(:last)').remove();

				// add the new list items and their event handlers
				for ( j=iStart ; j<=iEnd ; j++ ) {
					sClass = (j==oPaging.iPage+1) ? 'class="active"' : '';
					$('<li '+sClass+'><a href="#">'+j+'</a></li>')
						.insertBefore( $('li:last', an[i])[0] )
						.bind('click', function (e) {
							e.preventDefault();
							oSettings._iDisplayStart = (parseInt($('a', this).text(),10)-1) * oPaging.iLength;
							fnDraw( oSettings );
						} );
				}

				// add / remove disabled classes from the static elements
				if ( oPaging.iPage === 0 ) {
					$('li:first', an[i]).addClass('disabled');
				} else {
					$('li:first', an[i]).removeClass('disabled');
				}

				if ( oPaging.iPage === oPaging.iTotalPages-1 || oPaging.iTotalPages === 0 ) {
					$('li:last', an[i]).addClass('disabled');
				} else {
					$('li:last', an[i]).removeClass('disabled');
				}
			}
		}
	}
});
