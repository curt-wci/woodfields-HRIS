

<div class="modal fade" id="add_new_personnel_request_modal" tabindex="-1" role="dialog" 
     aria-labelledby="add_new_personnel_request_modal" aria-hidden="true" style="overflow: auto">
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
                
                <form class="form-horizontal" role="form" id="addPersonnelRequest">
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="job_title">Job Title:</label>
                    <div class="col-sm-10">
                        <select name="job_title" id="job_title" class="form-control">
                        </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="job_title">Requesting Department:</label>
                    <div class="col-sm-10">
                        <select name="req_dept" id="req_dept" class="form-control">
                        </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="job_title">Mobilization Date:</label>
                    <div class="col-sm-10">
                        <div class="input-append date form_datetime">
                            <input size="16" type="text" value="" readonly class="form-control" placeholder="Mobilization Date" name="mobi_date" required> <span class="add-on"><i class="icon-th"></i></span>
                        </div>
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
                <button type="button" class="btn btn-primary" id="submitPersonnelRequest">
                    Save changes
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$('#submitPersonnelRequest').click(function(e){
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
                var valToPass = $('#addPersonnelRequest').serializeArray();
                $.ajax({
                    url: "engine.php",
                    data: {function: "InsertNewPersonnelRequest", extra: valToPass},
                    success : function(data){
                        
                        $("#add_new_personnel_request_modal").modal("hide");
                        $("#main_table").DataTable().ajax.reload();
                        
                    }
                });
            } else {
                swal("Cancelled", "Your imaginary file is safe :)", "error");
            }
});

});
</script>