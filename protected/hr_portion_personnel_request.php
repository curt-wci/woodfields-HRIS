<div class="modal fade" id="hr_potion_request_modal" tabindex="-1" role="dialog" 
     aria-labelledby="hr_potion_request_modal" aria-hidden="true" style="overflow: auto">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" 
                   data-dismiss="modal">
                       <span aria-hidden="true">&times;</span>
                       <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    Personnel Requisition Form
                </h4>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body">
               <div>
                   <button class="btn btn-primary" id="hr_portion_status_addBtn">Add Status</button>
               </div>
                <div>
                    <table id="hr_portion_main_table">
                        <thead>
                            <th>Vacancy Status</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Additional Remarks</th>
                        </thead>
                    </table>
                </div> 
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add_hr_portion_detail_modal" tabindex="-1" role="dialog" 
     aria-labelledby="add_hr_portion_detail_modal" aria-hidden="true" style="overflow: auto">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" 
                   data-dismiss="modal">
                       <span aria-hidden="true">&times;</span>
                       <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    Add HR Portion Details
                </h4>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body">
                
                <form class="form-horizontal" role="form" id="hr_portion_details_form">
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="job_title">Vacancy Status:</label>
                    <div class="col-sm-10">
                        <select name="VACANCY_STATUS" id="VACANCY_STATUS" class="form-control">
                        <option value="1">Sourcing</option>
                        <option value="2">Interviewing</option>
                        <option value="3">Initial</option>
                        <option value="4">Dept/Unit</option>
                        <option value="5">Screening</option>
                        <option value="6">Medical</option>
                        <option value="7">Orientation</option>
                        </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="job_title">From:</label>
                    <div class="col-sm-10">
                        <div class="input-append date form_datetime">
                            <input size="16" type="text" value="" readonly class="form-control" placeholder="From" name="VACANCY_FROM"> <span class="add-on"><i class="icon-th"></i></span>
                        </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="job_title">Mobilization Date:</label>
                    <div class="col-sm-10">
                        <div class="input-append date form_datetime">
                            <input size="16" type="text" value="" readonly class="form-control" placeholder="To" name="VACANCY_TO"> <span class="add-on"><i class="icon-th"></i></span>
                        </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="job_title">Addtional Remarks:</label>
                    <div class="col-sm-10">
                       <input type="text" class="form-control" name="REMARKS" id="REMARKS">
                    </div>
                  </div>

                </form>
                
            </div>
            
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">
                            Close
                </button>
                <button type="button" class="btn btn-primary" id="submit_hr_portion_details">
                    Save changes
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var Request_id;
function hr_portion_display_data(request_id){
    Request_id = request_id;
    $('#hr_portion_main_table').DataTable( {
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
			"data": {'function': 'get_hr_potion_details', 'extra' : request_id},
			"type": "POST",
            "error" : function(){
                swal("Invalid Data","No data found on HR Portion.","error");
            }
			},
        "columns": [
            {"data" : "vacancy_status"},
            {"data" : "vacancy_from"},
            {"data" : "vacancy_to"},
            {"data" : "remarks"}
        ]
    });
}    
$('#hr_portion_status_addBtn').click(function(e){
    e.preventDefault();
    $('#add_hr_portion_detail_modal').appendTo("body").modal('show');
});
    
$('#submit_hr_portion_details').click(function(e){
   e.preventDefault();
     swal({
        title: "Are you sure you want to submit the form?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Okay",
        closeOnConfirm: false,
        closeOnCancel: false
    },
         function(isConfirm){
            if (isConfirm) {
                var valToPass = $('#hr_portion_details_form').serializeArray();
                valToPass.push({name: "request_id",value: Request_id});
                $.ajax({
                    type : 'POST',
                    url : 'engine.php',
                    data : {'function' : "add_hr_portion_details", 'extra' : valToPass},
                    success : function(data){
                        swal("Success","Operation has been executed properly","success");
                        $("#hr_portion_main_table").DataTable().ajax.reload();
                        $("#add_hr_portion_detail_modal").modal("hide");
                    }
                });
            }
            else{
                swal("Cancelled","Operation has been cancelled. Data is safe!","success");
            }
    });
});
</script>