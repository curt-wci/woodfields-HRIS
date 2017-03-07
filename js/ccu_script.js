$(document).ready(function(){
var currentTime = new Date();
var month = currentTime.getMonth() + 1;
var day = currentTime.getDate();
var year = currentTime.getFullYear();
var curr_date = year + "-" + month + "-" + day;
$(".form_datetime").datetimepicker({
    format: "yyyy-mm-dd",
    todayBtn: true,
    startDate: curr_date + " 00:00",
    minuteStep: 20,
    autoclose: true,
    showMeridian: true
});
    
$.ajax({
    url : "engine.php",
    data: {function: "getSessionStored", extra: ""},
    type : 'POST',
    success : function(data){
        var obj = JSON.parse(data);
        var sess_user_type = obj[0].user_type.toString();
        var sess_user_id = obj[1].logged_user.toString();
        
        if(sess_user_type == "EMPLOYEE"){
            $('#add_new_personnel_request').hide();
            $('#emp_request_label').hide();
            $('#main_table').hide();
        }
    }
});
    

var gastos_table,tbMain, approved_table, training_table, HR_P;
    var rs = [];
    var train_rs = [];
    var resultSet = [];
    var id =null;
    tbMain = $('#main_table').DataTable({
			"bDestroy": true,
			"searching": false,
            "bPaginate": false,
			"ordering": false,
			"bLengthChange": true,
            "bInfo": false,
			"select": false,
			"pageLength": 100,
            "responsive" : true,
			"ajax": { "url": "engine.php",
					  "data": { "function": "getAllPersonnelRequest","extra": "true" },
					  "type": "POST"
					},
			"columns": [
				{
					"data":           null,
					"orderable": 	false,
					"defaultContent": "<center><button id='deleteBtn_Main' type='button' class='btn btn-xs'><span class='glyphicon glyphicon-trash'></span></button></center>"
				},
				{
					"data":           null,
					"orderable": 	false,
					"defaultContent": "<center><button id='editBtn_Main' type='button' class='btn btn-xs'><span class='glyphicon glyphicon-pencil'></span></button></center>"
				},
				{ "data" : "s_deptdesc" },
				{ "data" : "s_posidesc" },
                {"data"   : "",
                 "render" :  function(data, type, mySource, meta){
                     return date_parser(mySource.request_date);
                 } },
				{"data"   : "",
                 "render" :  function(data, type, mySource, meta){
                     return date_parser(mySource.d_mobiDate);
                 } },
                {
					"data":           null,
					"orderable": 	false,
					"defaultContent": "<center><button id='reqDetailBtn_Main' type='button' class='btn btn-xs'><span class='glyphicon glyphicon-th'></span></button></center>"
				},
                {
					"data":           null,
					"orderable": 	false,
					"defaultContent": "<center><button id='approvalsBtn_Main' type='button' class='btn btn-xs'><span class='glyphicon glyphicon-user'></span></button></center>"
				}
			]
		});
    training_table = $('#training_request_table').DataTable( {
       "bDestroy": true,
		"searching": false,
        "bPaginate": false,
		"ordering": false,
		"bLengthChange": true,
        "bInfo": false,
		"select": false,
		"pageLength": 100,
        "responsive" : true,
        "ajax": {
			"url": "engine.php",
			"data": {'function': 'getAllTrainingRequestByUserType', 'extra' : ''},
			"type": "POST",
            "error" : function(){
                swal("Invalid Data","No data found on employee training request.","error");
                $('#training_request_table').hide();
                $('#trainings_label').hide();
            }
			},
        "columns": [
            {"data" : "s_trntitle"},
            {"data"   : "",
             "render" :  function(data, type, mySource, meta){
                 return mySource.s_lastname+" "+mySource.s_frstname;
             } },
            {"data"   : "",
             "render" :  function(data, type, mySource, meta){
                 return mySource.s_posidesc+"<br>"+mySource.s_deptdesc;
             } },
            {"data"   : "",
             "render" :  function(data, type, mySource, meta){
                 return date_parser(mySource.d_strtdate)+"<br>"+date_parser(mySource.d_end_date);
             } },
            {"data" : "n_traincst"},
            {"data" : "s_trainorg"},
            {"data" : "s_location"},
            {"data" : "n_charging"},
            {"data" : "s_reqstats"},
            {
					"data":           null,
					"orderable": 	false,
					"defaultContent": "<center><button id='view_training_btn' type='button' class='btn btn-xs'><span class='glyphicon glyphicon-user'></span></button></center>"
				}
        ]
    });
    HR_P = $('#HR_PORTION_table').DataTable({
			"bDestroy": true,
            "pageLength": 50,
            "responsive" : true,
            "ajax": { "url": "engine.php",
					  "data": { "function": "getPersonnelRequest_Report","extra": "" },
					  "type": "POST"
					},
			"columns": [
				{ "data" : "s_deptdesc" },
                { "data" : "s_posidesc" },
                {"data"   : "",
                 "render" :  function(data, type, mySource, meta){
                     
                     return date_parser(mySource.request_date);
                 } },
                {"data"   : "",
                 "render" :  function(data, type, mySource, meta){
                     
                     return date_parser(mySource.d_mobiDate);
                 } },
                {"data"   : "",
                 "render" :  function(data, type, mySource, meta){
                     if(isNull(mySource.employment_type))
                         return "<font color='red'>No data</font>";
                     else
                         return mySource.employment_type;
                 }},
                {
                "data" : "",
                 "render" :  function(data, type, mySource, meta){
                     if(isNull(mySource.emp_dur_from) || isNull(mySource.emp_dur_to))
                         return "<font color='red'>No data</font>";
                     else
                         return date_parser(mySource.emp_dur_from)+" - "+date_parser(mySource.emp_dur_to);
                 } },
                {"data"   : "",
                 "render" :  function(data, type, mySource, meta){
                     if(isNull(mySource.replaced_by))
                         return "<font color='red'>No data</font>";
                     else
                         return mySource.replaced_by;
                 }},
                {"data"   : "",
                 "render" :  function(data, type, mySource, meta){
                     var content;
                     
                     if(isNull(mySource.isBudget) || isNull(mySource.budget_clearance) || isNull(mySource.budget_clearance_date))
                         return "<font color='red'>No data</font>";
                     else{
                         var content = checkNull(mySource.isBudget) + "<br>"+
                         checkNull(mySource.budget_clearance)+"<br>"+
                         checkNull(mySource.budget_clearance_date)+"<br>";
                     }
                     return content;
                 } },
                {"data"   : "",
                 "render" :  function(data, type, mySource, meta){
                     if(isNull(mySource.justification))
                         return "<font color='red'>No data</font>";
                     else
                         return mySource.justification;
                 }},
                {"data"   : "",
                 "render" :  function(data, type, mySource, meta){
                     if(isNull(mySource.job_summary))
                         return "<font color='red'>No data</font>";
                     else
                         return mySource.job_summary;
                 }},
                {"data"   : "",
                 "render" :  function(data, type, mySource, meta){
                     if(isNull(mySource.qual_other_req))
                         return "<font color='red'>No data</font>";
                     else
                         return mySource.qual_other_req;
                 }},
                {"data"   : "",
                 "render" :  function(data, type, mySource, meta){
                     //mySource.isApproveAdmin + "<br>" + mySource.isApproveEVP + "<br>" + mySource.isApproveCEO
                     var admin = checkNull(mySource.isApproveAdmin);
                     var coo = checkNull(mySource.isApproveEVP);
                     var ceo = checkNull(mySource.isApproveCEO);
                     
                     return "<center>"+admin + " " + coo + " " + ceo + "</center>";
                 } },
                {
                    "data":           null,
                    "orderable": 	false,
                    "defaultContent": "<center><button id='HRPortionBtn_HRPortion' type='button' class='btn btn-xs'><span class='glyphicon glyphicon-user'></span></button></center>"
				}
                
			]
		});
    
    

$.ajax({
    url: "engine.php",
    data: {function: "getAllPosition", extra: ""},
    type : 'POST',
    success : function(data){
        var obj = JSON.parse(data);
        for(var i = 0; i<= obj.data.length -1; i++){
            $("#job_title").append($('<option>', { 
                value: obj.data[i].n_posinmbr,
                text: obj.data[i].s_posidesc
            }));
        }
    }
});   
$.ajax({
    url: "engine.php",
    type : 'POST',
    data: {function: "getAllDepartment", extra: ""},
    success : function(data){
        var obj = JSON.parse(data);
        for(var i = 0; i<= obj.data.length -1; i++){
            $("#req_dept").append($('<option>', { 
                value: obj.data[i].n_deptnmbr,
                text: obj.data[i].s_deptdesc
            }));
        }
    }
});   
//$.ajax({
//    url: "engine.php",
//    data: {function: "getAllPosition", extra: ""},
//    success : function(data){
//        var obj = JSON.parse(data);
//        for(var i = 0; i<= obj.data.length -1; i++){
//            $("#n_posnumbr").append($('<option>', { 
//                value: obj.data[i].n_posinmbr,
//                text: obj.data[i].s_posidesc
//            }));
//        }
//    }
//});
//$.ajax({
//    url: "engine.php",
//    data: {function: "getAllDepartment", extra: ""},
//    success : function(data){
//        var obj = JSON.parse(data);
//        for(var i = 0; i<= obj.data.length -1; i++){
//            $("#n_deptnmbr").append($('<option>', { 
//                value: obj.data[i].n_deptnmbr,
//                text: obj.data[i].s_deptdesc
//            }));
//        }
//    }
//});


//---- button clicked in datatable ------
$('#main_table tbody').on( 'click', 'button', function () {
			rs = tbMain.row( $(this).parents('tr') ).data();
            switch(this.id){
                case 'deleteBtn_Main':
                    swal({
                        title: "Are you sure you want to delete this request?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Okay, I understand",
                        closeOnConfirm: false,
                        closeOnCancel: false
                    },
                         function(isConfirm){
                        if (isConfirm) {                           
                            $.ajax({
                                type : 'POST',
                                url : 'engine.php',
                                data : {'function' : "deleteRequest", 'extra' : rs['n_requestId']},
                                success : function(data){
                                        swal("Deleted!", "Request has been deleted", "success");
                                        $("#main_table").DataTable().ajax.reload();
                                }
                            });
                        } else {
                            swal("Cancelled", "Your imaginary file is safe :)", "error");
                        }
                    });
                    break;
                case 'editBtn_Main' :
                        $('#edit_personnel_request_modal').appendTo("body").modal('show');
                        editPersonnelRequestForm(rs['n_requestId']);
                    
                    break;
                case 'approvalsBtn_Main':
                    $('#get_approvals_modal').modal('show');
                    getApprovals(rs['n_requestId']);
                    break;
                case 'reqDetailBtn_Main':
                    $('#get_request_details_modal').modal('show');
                    getRequestDetailsFunction(rs['n_requestId']);
                    break;
                
                case 'HRPortionBtn_Main':
                    $('#hr_potion_request_modal').appendTo("body").modal('show');
                    hr_portion_display_data(rs['n_requestId']);
                    break;
            }
			
});

$('#training_request_table').on('click','button',function(){
    train_rs = training_table.row($(this).parents('tr')).data();
    
    switch(this.id){
        case "view_training_btn":
            $(location).attr('href','/view_training.php?tr_name='+train_rs['s_trntitle']);
            
        break;
    }
});

$('#HR_PORTION_table tbody').on( 'click', 'button', function () {
    resultSet = HR_P.row( $(this).parents('tr') ).data();
    switch(this.id){
        
        case 'approvalsBtn_HRPortion':
            $('#get_approvals_modal').modal('show');
            getApprovals(resultSet['n_requestId']);
            break;
        
        case 'reqDetailBtn_HRPortion':
            console.log("It works!");
            $('#get_request_details_modal').modal('show');
            getRequestDetailsFunction(resultSet['n_requestId']);
            break;
            
        case 'HRPortionBtn_HRPortion':
            var count = 0;
            if(isNull(resultSet['isApproveAdmin']) == false)
                count ++;
            if(isNull(resultSet['isApproveEVP']) == false)
                count ++;
            if(isNull(resultSet['isApproveCEO']) == false)
                count ++;
            
            if(count == 0){
                swal("Invalid Request","HR Portion is locked. Please complete the signatories in the approval section","error");
            }else{
              $('#hr_potion_request_modal').appendTo("body").modal('show');
            hr_portion_display_data(resultSet['n_requestId']);  
            }
            break;
        
    }
			
});
    
$('#get_request_details_modal').on('shown.bs.modal',function(){
        $(this).find('.modal-dialog').css({width:'80%', height:'auto', 'max-height':'100%'});
});
$('#get_approvals_modal').on('shown.bs.modal',function(){
        $(this).find('.modal-dialog').css({width:'80%', height:'auto', 'max-height':'100%'});
}); 
$('#hr_potion_request_modal').on('shown.bs.modal',function(){
    $(this).find('.modal-dialog').css({width:'80%', height:'auto', 'max-height':'100%'});
});    
$('#approve_request').click(function(e){
    e.preventDefault();
    var sess_token, sess_user;
    $.ajax({
    url: "engine.php",
    type : 'POST',
    data: {function: "getSessionStored", extra: ""},
    success : function(data){
        var obj = JSON.parse(data);
         sess_token = obj[0].user_type.toString();
         sess_user = obj[1].logged_user.toString();
        
            if(sess_token === "TOP_MNGT"){
        swal({
            title: "Are you sure?",
            text: "You want to perform the action?",
            type: "info",
            showCancelButton: true,
            confirmButtonText: "Okay",
            cancelButtonText: "No",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm){
            if (isConfirm) {
                $.ajax({
                    type : 'POST',
                    url : 'engine.php',
                    data : {"function" : "approvePersonnelRequest", "extra" : sess_token+" " +sess_user + " "+rs['n_requestId']},
                    success : function(data){
                        swal("Success", "Personnel request has been approved! Wait for other signatories", "success");
                        $("#request_approval_table").DataTable().ajax.reload();
                    }
                });
                
            } else {
                swal("Cancelled", "Your imaginary file is safe :)", "error");
            }
});
    }
    else{
        swal("Error", "You dont have an access to this module yet", "error");
    }
        
    }
});


});
$('#add_new_personnel_request').click(function(e){
    e.preventDefault();
    $('#add_new_personnel_request_modal').appendTo("body").modal('show');
});
$('#request_details_add_btn').click(function(e){
    e.preventDefault();
    $('#add_new_personnel_request_details_modal').appendTo("body").modal('show');
    sessionStorage.setItem("request_id",rs['n_requestId']);
    clear_data();
});
$('#request_details_delete_btn').click(function(e){
    e.preventDefault();
    swal({
        title: "Are you sure you want to delete this request?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Okay, I understand",
        closeOnConfirm: false,
        closeOnCancel: false
    },
         function(isConfirm){
            if (isConfirm) {
                $.ajax({
                    type : 'POST',
                    url : 'engine.php',
                    data : {'function' : "deleteRequestDetails", 'extra' : rs['n_requestId']},
                    success : function(data){
                        swal("Deleted!", "Request details has been deleted", "success");
                        $("#get_request_details_modal").modal("hide");
                    }
                });
            }
            else{
                swal("Cancelled","Operation has been cancelled. Data is safe!","success");
            }
    });
    
});  
$('#request_details_edit_btn').click(function(e){
    e.preventDefault();
    $('#add_new_personnel_request_details_modal').appendTo("body").modal('show');
    sessionStorage.setItem("request_id",rs['n_requestId']);
    editPersonnelRequest(rs['n_requestId']);
});
    
    

    
}); // End of document.ready

function checkNull(data){
    if(data == "" || data === undefined || data == null || data == "null"){
        return "";
    }
    else
        return '<span class="glyphicon glyphicon-check"></span>';
}

function isNull(data){
    if(data == "" || data === undefined || data == null || data == "null"){
        return true;
    }
    else
        return false;
}

function date_parser(date_string){
    if(date_string == "" || date_string === undefined)
        return "Data not specified";
    else{
        var monthIndex = date_string.slice(6,7);
    var year = date_string.slice(0,4);
    var day = date_string.slice(8,10);
    
    var monthNames = [' ','January','February','March','April','May','June','July','August','September','October','November','December'];
    
    return monthNames[monthIndex]+" "+day+", "+year;
    }
    
}
    
function getRequestDetailsFunction(id){
    request_details_table = $('#request_details_table').DataTable( {
        "bDestroy": true,
        "searching": false,
        "bPaginate": false,
        "ordering": false,
        "bLengthChange": true,
        "bInfo": false,
        "select": false,
        "pageLength": 100,
        "ajax": {
			"url": "engine.php",
			"data": {'function': 'getRequestDetails', 'extra' : id},
			"type": "POST",
            "error": function(){
                swal("Invalid Data","No data found.","error");
            }
			},
        "columns": [
            {"data" : "employment_type"},
            {"data"	: "emp_dur_from"},
            {"data" : "emp_dur_to"},
            {"data" : "replaced_by"},
            {"data" : "isBudget"},
            {"data" : "budget_clearance"},
            {"data" : "budget_clearance_date"}
        ]
    });
    $('#request_details_table2').DataTable( {
        "bDestroy": true,
        "searching": false,
        "bPaginate": false,
        "ordering": false,
        "bLengthChange": true,
        "bInfo": false,
        "select": false,
        "pageLength": 100,
        "ajax": {
			"url": "engine.php",
			"data": {'function': 'getRequestDetails', 'extra' : id},
			"type": "POST",
            "error" : function(){
                swal("Invalid Data","No data found.","error");
            }
			},
        "columns": [
            {"data" : "justification"}
        ]
    });
    $('#request_details_table3').DataTable( {
        "bDestroy": true,
        "searching": false,
        "bPaginate": false,
        "ordering": false,
        "bLengthChange": true,
        "bInfo": false,
        "select": false,
        "pageLength": 100,
        "ajax": {
			"url": "engine.php",
			"data": {'function': 'getRequestDetails', 'extra' : id},
			"type": "POST",
            "error" : function(){
                swal("Invalid Data","No data found.","error");
            }
			},
        "columns": [
            {"data" : "job_summary"}
        ]
    });
    $('#request_details_table4').DataTable( {
        "bDestroy": true,
        "searching": false,
        "bPaginate": false,
        "ordering": false,
        "bLengthChange": true,
        "bInfo": false,
        "select": false,
        "pageLength": 100,
        "ajax": {
			"url": "engine.php",
			"data": {'function': 'getRequestDetails', 'extra' : id},
			"type": "POST",
            "error" : function(){
                swal("Invalid Data","No data found.","error");
            }
			},
        "columns": [
            {"data" : "qual_other_req"}
        ]
    });
}
    
function getApprovals(id){
    approved_table = $('#request_approval_table').DataTable( {
        "bDestroy": true,
        "searching": false,
        "bPaginate": false,
        "ordering": false,
        "bLengthChange": true,
        "bInfo": false,
        "select": false,
        "pageLength": 100,
        "ajax": {
			"url": "engine.php",
			"data": {'function': 'getApproval', 'extra' : id},
			"type": "POST"
			},
        "columns": [
            {"data" : "isApproveAdmin"},
            {"data"	: "isApproveEVP"},
            {"data" : "isApproveCEO"}
        ]
    });
}