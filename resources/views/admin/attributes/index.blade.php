@extends('admin.layouts.app')

@section('content')
<!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Manage Attributes</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Attribute Lists</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="card card-primary">
					<div class="card-header">
					  <h3 class="card-title">Manage Attributes</h3>

					  <div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
						  <i class="fas fa-minus"></i>
						</button>
					  </div>
					</div>
					<div class="card-body">
						<table id="attribute_table" class="table table-bordered table-striped">
			              <thead>
			                  <tr>
			                    <th>Attribute Name</th>
			                    <th>Attribute Values</th>
			                    <th>Action</th>
			                  </tr>
							</thead>
							<tbody>  
		                      @foreach($all_attributes as $row)
                          @php
                            $attribute_values = json_decode($row->attribute_value);
                            //print_r($attribute_values);
                          @endphp   
		                     <tr>            
		                        <td>{{ $row->attribute_name }}</td> 
		                        <td align="center">
		                        	@foreach($attribute_values as $values)
                                <button class="btn btn-sm btn-success" style="margin:0 5px;">{{ $values }}</span>
                              @endforeach
		                        </td>
		                        <td>
		                          <button onclick="window.location='{{ url('/')}}/admin/category/{{$row->attribute_row_id }}/edit'" class="btn btn-sm btn-warning mb-2">Edit</button>
		                          <form id="deleteCategory_{{$row->attribute_row_id }}" action="{{ url('/')}}/admin/category/{{$row->attribute_row_id }}" style="display: inline;" method="POST">
		                          	{{ method_field('DELETE') }}
            						        @csrf
		                          	<input class="btn btn-sm btn-danger deleteLink" category_name="{{ $row->category_name }}" attribute_row_id ="{{$row->attribute_row_id }}" data-toggle="modal" data-target="#category-delete-modal" deleteID="{{$row->attribute_row_id }}" value="Delete" style="width: 100px; margin-top: -8px;">
		                          </form>
		                        </td>                        
		                      </tr>
		                    @endforeach
							</tbody>
						</table>
					</div>
			  <!-- /.card -->
				</div>

			</div>
		</div>
    </section>
    <!-- /.content -->
</div>
<div class="modal modal-danger fade" id="category-delete-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Delete Category</h4>
      </div>
      <div class="modal-body">
        <p>Do you really want to delete <b class="catname"></b> category?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-outline submitDeleteModal">Submit</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- DataTables  & Plugins -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<script type="text/javascript">
  $(document).ready(function() {
      $('#attribute_table').DataTable({
      	"order": [],
      });

      $('.deleteLink').click(function(){
      	var category_name = $(this).attr('category_name');
      	var attribute_row_id  = $(this).attr('attribute_row_id ');
      	console.log(category_name);
      	$('#category-delete-modal .catname').empty();
      	$('#category-delete-modal .catname').append(category_name);
      	$('#category-delete-modal .submitDeleteModal').attr('attribute_row_id ', attribute_row_id );
      });

      $('.submitDeleteModal').click(function(){
      	var attribute_row_id  = $(this).attr('attribute_row_id ');
      	$('#deleteCategory_'+attribute_row_id ).submit();
      });
  });
</script>
@endsection