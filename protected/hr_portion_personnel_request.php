

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
                   <button class="btn btn-primary">Add Status</button>
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

<script>                                       
function hr_portion_display_data(request_id){
    console.log(request_id);
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
			"type": "GET",
            "error" : function(){
                swal("Invalid Data","No data found on employee training request.","error");
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
    
</script>