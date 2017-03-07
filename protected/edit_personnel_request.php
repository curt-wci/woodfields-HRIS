

<div class="modal fade" id="edit_personnel_request_modal" tabindex="-1" role="dialog" 
     aria-labelledby="edit_personnel_request_modal" aria-hidden="true" style="overflow: auto">
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
                
                <form class="form-horizontal" role="form" id="EditPersonnelRequest">
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="job_title">Job Title:</label>
                    <div class="col-sm-10">
                        <select name="n_posnumbr" id="n_posnumbr" class="form-control">
                        </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="job_title">Requesting Department:</label>
                    <div class="col-sm-10">
                        <select name="n_deptnmbr" id="n_deptnmbr" class="form-control">
                        </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label  class="col-sm-2 control-label"
                              for="job_title">Mobilization Date:</label>
                    <div class="col-sm-10">
                        <div class="input-append date form_datetime">
                            <input size="16" type="text" value="" readonly class="form-control" placeholder="Mobilization Date" name="d_mobiDate" required> <span class="add-on"><i class="icon-th"></i></span>
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
                <button type="button" class="btn btn-primary" id="submitEditPersonnelRequest">
                    Save changes
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var req_ID;
$('#submitEditPersonnelRequest').click(function(e){
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
                var valToPass = $('#EditPersonnelRequest').serializeArray();
                valToPass.push({name:'n_requestId',value: req_ID});
                $.ajax({
                    url: "engine.php",
                    type : 'POST',
                    data: {function: "EditPersonnelRequest", extra: valToPass},
                    success : function(data){
                        $("#edit_personnel_request_modal").modal("hide");
                        $("#main_table").DataTable().ajax.reload();
                        
                    }
                });
            } else {
                swal("Cancelled", "Your imaginary file is safe :)", "error");
            }
});
});
    
function editPersonnelRequestForm(data){
    req_ID = data;
     $.ajax({
        url: "engine.php",
        type : 'POST',
        data: {function: "getPersonnelRequestById", extra: data},
        success : function(data){
            console.log(data);
           var obj = JSON.parse(data);
            
            $.each(obj.data[0],function(index,value){
                document.getElementsByName(index)[0].value = value ;
            });
        }
    });
}
    
//function clear_data(){
//$('#addPersonnelRequestDetails').closest('form').find("input[type=text], textarea, select").val("");
//}
</script>