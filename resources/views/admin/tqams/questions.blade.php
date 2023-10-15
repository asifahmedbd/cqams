@extends('admin.layouts.app')

@section('content')
  <link href="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.css" rel="stylesheet">
  <script src="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <style>
    .select2-container{
      width: 170px !important;
    }
    .select2-hidden-accessible{
      border-color: #c7cdd4 !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
      border: 1px solid #c7cdd4 !important;
    }
  </style> 
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">Manage Questions</h4>

      <div class="row">
        <!-- Basic Layout -->
        <div class="col-xxl">
          <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
            </div>
            <div class="card-body">
                <div class="col-md-12 bg-light mb-3" style="text-align: right;">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#basicModal">Add Question</button>
                </div>
                
                <div class="table-responsive text-nowrap">
                  <table class="table table-bordered" id="cqams_questions">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Category</th>
                        <th>Question Title (En)</th>     
                        <th>Question Title (Bn)</th>
                        <th>Type</th>
                        <th style="text-align:center;">Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                          <?php $i=1; ?>
                          @foreach($evaluation_fields as $row)
                            <tr>
                              <td>{{$i}}</td>
                              <td>{{$row->main_category->criteria_name_en}} ({{$row->sub_category->criteria_name_en}})</td>
                              <td>{{$row->field_name_en}}</td>
                              <td>{{$row->field_name_bn}}</td>
                              <td>
                                @if(isset($row->inst_type))
                                  @foreach($row->inst_type as $key=>$value)
                                    <span class="badge bg-secondary">{{$instTypes[$value]}}</span>
                                  @endforeach
                                @endif
                              </td>
                              <td style="text-align:center;">
                                <div class="btn-group btn-group-xs btn-group-solid" >
                                  <button id="editField" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#UpdateModal" qid="{{$row->evaluation_field_row_id}}">Edit</button>
                                  <a class="btn btn-danger deleteField" href="#modal-center"  sid="{{$row->id}}" data-toggle="modal"> Delete </a>
                                </div>
                              </td>
                            </tr>
                          <?php $i++; ?>  
                          @endforeach
                    </tbody>
                  </table>
                </div>
            </div>
          </div>
        </div>
      </div>


      <!-- Modal -->
      <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
          <form action="{{ url('/') }}/admin/tqams/storeFieldsMultiple" method="POST" class="form-horizontal">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Add Questions</h5>
                <button
                  type="button"
                  class="btn-close"
                  data-bs-dismiss="modal"
                  aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-3 mb-3">
                    <label for="exampleFormControlSelect1" class="form-label">Category<span class="text-danger">*</span></label>
                      <select class="form-select field_type" name="field_type" required>
                        <option value="" selected="select" disabled="disabled">Select Category</option>
                        @foreach($category as $row=>$val)
                        <option value="{{$val->evalution_criteria_row_id}}">{{$val->criteria_name_en}}</option>
                        @endforeach             
                      </select>
                  </div>
                  <div class="col-md-3 mb-3">
                    <label for="exampleFormControlSelect1" class="form-label">Sub Category<span class="text-danger">*</span></label>
                      <select  class="form-select criteria_type" name="criteria_type" required>
                      </select>
                  </div>
                  <div class="col-md-2 mb-3">
                    <label for="exampleFormControlSelect1" class="form-label">Scale<span class="text-danger">*</span></label>
                      <select  class="form-select" name="scale" required>
                        @foreach($scale as $row=>$val)
                        <option value="{{$val->id}}">{{$val->name}}</option>
                        @endforeach   
                      </select>
                  </div>
                  <div class="col-md-2 mb-3">
                    <label for="exampleFormControlSelect2" class="form-label">Type<span class="text-danger">*</span></label>
                      <select class="js-example-basic-multiple form-select" name="inst_type[]" multiple="multiple">
                        <option value="">Select type</option>
                          @foreach($type as $types=>$typ)
                              <option value="{{$typ->id}}">{{$typ->name}}</option>
                          @endforeach
                      </select>
                  </div>
                  <div class="col-md-2 mb-3">
                    <label for="exampleFormControlSelect1" class="form-label">Number of Question</label>
                      <div class="col-sm-12">
                        <input type="text" class="form-control" name="major_criteria" id="criteria" placeholder="Number of Question" required>
                      </div>
                  </div>
                </div>
                <div class="row">
                  <div id="getForm" class="box-body"></div>
                </div>
               </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                  Close
                </button>
                <button type="submit" class="btn btn-primary submitForm">Save changes</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="modal fade" id="UpdateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
          <form action="{{ url('/') }}/admin/tqams/question/update" method="POST" class="form-horizontal">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Update Question</h5>
                <button
                  type="button"
                  class="btn-close"
                  data-bs-dismiss="modal"
                  aria-label="Close"></button>
              </div>
              <div class="modal-body">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                  Close
                </button>
                <button type="submit" class="btn btn-primary submitForm">Update</button>
              </div>
            </div>
          </form>
        </div>
      </div>


  </div>
  <script type="text/javascript">
    $(document).ready(function() {

        $('.js-example-basic-multiple').select2({
          dropdownParent: $('#basicModal')
        });

        new DataTable('#cqams_questions');

        //$('.field_type').change(function(){
        $(document).on('click','.field_type',function(e) {
          var criteria_row_id = $(this).val();
            $.ajax({
                url: "{{ url('get-tqams-criteria/') }}"+ '/'+ criteria_row_id,
                type: "GET",
                dataType: "html",
                success: function(data){
                  $('.criteria_type').empty();                
                  $('.criteria_type').append(data);
                }
            });
        });


        $('#criteria').keyup(function(){
          var number = $(this).val();
          console.log(number);
          if (number) {
            $.ajax({
                url: "{{ url('getTqamsQuestionform/') }}"+ '/'+ number,
                type: "GET",
                dataType: "html",
                success: function(data){
                    $('#getForm').empty();                     
                    $('#getForm').append(data);
                }
            });
          }
          else{
            $('#getForm').empty();      
          }
        });

        $(document).on('click','#editField',function(e) {
            var qid = $(this).attr('qid');
            $.ajax({
                url: "{{ url('get-question-details/') }}"+ '/'+ qid,
                type: "GET",
                dataType: "html",
                success: function(data){
                    $('#UpdateModal .modal-body').empty();
                    $('#UpdateModal .modal-body').append(data);
                }
            });
            $("#UpdateModal .modal-footer").after('<input type="hidden" class="up" name="evaluation_row_id" value="'+qid+'" >');
        })

    });
  </script>
@endsection