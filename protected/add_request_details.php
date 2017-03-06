
<div class="modal fade" id="add_new_personnel_request_details_modal" tabindex="-1" role="dialog" 
     aria-labelledby="add_new_personnel_request_details_modal" aria-hidden="true" style="overflow: auto">
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
                    Modal title
                </h4>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body">
                
                <form class="form-horizontal" role="form" id="addPersonnelRequestDetails">
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="employType">Job Title:</label>
                    <div class="col-sm-10">
                        <select name="employment_type" id="employment_type" class="form-control">
                            <option value="Regular">Regular</option>
                            <option value="ProjBased">Project-Based</option>
                            <option value="Contractual">Contractual</option>
                            <option value="PracTrainee">Practicum Trainee</option>
                            <option value="Replacement">Replacement</option>
                            <option value="NewVacancy">New Vacancy</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="job_title">Replaced By</label>
                    <div class="col-sm-10">
                       <input type="text" class="form-control" name="replaced_by" placeholder="** This field is for replacement employment type only">
                    </div>
                  </div>
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="empDuration">Employment Duration:</label>
                    <div class="col-sm-10">
                        <div class="input-append date form_datetime">
                            <input size="16" type="text" value="" readonly class="form-control" placeholder="From" name="emp_dur_from" required> <span class="add-on"><i class="icon-th"></i></span>
                        </div>
                        <div class="input-append date form_datetime">
                            <input size="16" type="text" value="" readonly class="form-control" placeholder=Until name="emp_dur_to" required> <span class="add-on"><i class="icon-th"></i></span>
                        </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="job_title">Is Budget Included in FY Plan</label>
                    <div class="col-sm-10">
                        <select name="isBudget" id="isBudget" class="form-control">
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="job_title">Budgetary Clearance</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="budget_clearance" required>
                    </div>
                  </div>
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="job_title">Date</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="budget_clearance_date" placeholder="YYYY-mm-dd Format only!!" required>
                    </div>
                  </div>
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="job_title">Justification</label>
                    <div class="col-sm-10">
                        <textarea name="justification" id="justification" rows="5" class="form-control" style="resize: none"></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="job_title">Job Summary</label>
                    <div class="col-sm-10">
                        <textarea name="job_summary" id="job_summary" rows="5" class="form-control" style="resize: none"></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="job_title">Qualification and Other Requirements</label>
                    <div class="col-sm-10">
                        <textarea name="qual_other_req" id="qual_other_req" rows="5" class="form-control" style="resize: none"></textarea>
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
                <button type="button" class="btn btn-primary" id="submitPersonnelRequestDetails">
                    Save changes
                </button>
            </div>
        </div>
    </div>
</div>

<script>    
$('#submitPersonnelRequestDetails').click(function(e){
    e.preventDefault();
    swal({
        title: "Submit Form",
        text: "Are you sure you want to submit this request?",
        type: "info",
        showCancelButton: true,
        confirmButtonText: "Okay!",
        closeOnConfirm: true
    },
         function(isConfirm){
            if (isConfirm) {
                var valToPass = $('#addPersonnelRequestDetails').serializeArray();
                valToPass.push({name:'n_requestId',value: sessionStorage.getItem("request_id")});
                $.ajax({
                    url: "engine.php",
                    data: {function: "updatePersonnelRequest", extra: valToPass},
                    success : function(data){
                        console.log(data);
                        $("#add_new_personnel_request_details_modal").modal("hide");
                        $("#request_details_table").DataTable().ajax.reload();
                        $("#request_details_table2").DataTable().ajax.reload();
                        $("#request_details_table3").DataTable().ajax.reload();
                        $("#request_details_table4").DataTable().ajax.reload();
                        sessionStorage.clear();
                        
                    }
                });
                
            } else {
                swal("Cancelled", "Your imaginary file is safe :)", "error");
            }
});

});
    
$('#add_new_personnel_request_details_modal').on('shown.bs.modal',function(){
        $(this).find('.modal-dialog').css({width:'50%', height:'auto', 'max-height':'100%'});
});

function editPersonnelRequest(data){
     $.ajax({
        url: "engine.php",
        data: {function: "getRequestDetails2", extra: data},
        success : function(data){
           var obj = JSON.parse(data);
            
            $.each(obj.data[0],function(index,value){
                document.getElementsByName(index)[0].value = value ;
            });
            
        }
    });
}
    
function clear_data(){
$('#addPersonnelRequestDetails').closest('form').find("input[type=text], textarea, select").val("");
}
</script>