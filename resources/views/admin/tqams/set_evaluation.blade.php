@extends('admin.layouts.app')

@section('content')
  <link href="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.css" rel="stylesheet">
  <script src="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.js"></script>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">CQAMS Institution-wise Evaluation Form</h4>

      <div class="row">
        <!-- Basic Layout -->
        <div class="col-xxl">
          <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
            </div>
            <div class="card-body">
                <div class="col-md-12 bg-light mb-3" style="text-align: right;">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#basicModal">Add Evaluation</button>
                </div>
                
                <div class="table-responsive text-nowrap">
                  <table class="table table-bordered" id="scale_list">
                    <thead>
                      <tr>
                        <th>Session</th>                  
                        <th>School</th>                  
                        <th>Type</th>
                        <th>Language</th>
                        <th>Month</th>
                        <th>Status</th>
                        <th style="text-align:center;">Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                          @foreach($all_data as $row)
                            <tr>
                              <td>{{$row->academic_session->academic_session_year}}-{{$month[$row->academic_session->month]}}</td>
                              <td>{{$row->school_info->school_name}}</td>
                              <td>{{$instTypes[$row->school_type]}}</td>
                              <td>
                                @if($row->eval_lang == 1)
                                  <span>Bangla</span>
                                @else
                                  <span>English</span>
                                @endif
                              </td>
                              <td>{{$month[$row->month]}}</td>
                              <td>
                                @if($row->entry_status == 0)
                                  <span class="badge bg-warning">Pending</span>
                                @else
                                  <span class="badge bg-success">Submitted</span>
                                @endif
                              </td>
                              <td style="text-align:center;">
                                <div class="btn-group btn-group-xs btn-group-solid" >
                                  <button class="btn btn-primary btn-sm" onclick="window.location='{{ url("/admin/submitEvaluation/$row->cse_id") }}'">
                                    Submit Evaluation
                                  </button>
                                  <button id="editField" class="btn btn-warning btn-sm" onclick="window.location='{{ url("/admin/submitEvaluation/$row->cse_id") }}'">Edit</button>
                                  <a class="btn btn-danger deleteField" href="#modal-center"  sid="{{$row->id}}" data-toggle="modal"> Delete </a>
                                </div>
                              </td>
                            </tr>
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
        <div class="modal-dialog" role="document">
          <form action="{{ url('/') }}/admin/addEvaluation" method="POST" class="form-horizontal">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Add Evaluation</h5>
                <button
                  type="button"
                  class="btn-close"
                  data-bs-dismiss="modal"
                  aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col mb-3">
                    <label for="nameBasic" class="form-label">Select Session</label>
                    <select  class="form-select academi_session" name="academi_session" required>
                      <option value="0">Select Session</option>
                      @foreach($all_session as $sdata)
                      <option value="{{$sdata->academic_session_row_id}}">{{$sdata->academic_session_year}}-{{ $month[$sdata->month] }}</option>
                      @endforeach   
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="col mb-3">
                    <label for="nameBasic" class="form-label">Select School</label>
                    <select  class="form-select school_info" name="school" required>
                      <option value="0">Select School</option>
                      @foreach($schools as $scdata)
                      <option value="{{$scdata->school_id}}">{{$scdata->school_name}}</option>
                      @endforeach   
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="col mb-3">
                    <label for="nameBasic" class="form-label">Select Type</label>
                    <select  class="form-select school_type" name="school_type" required>
                        
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="col mb-3">
                    <label for="nameBasic" class="form-label">Select Language</label>
                    <select  class="form-select eval_language" name="eval_language" required>
                        <option value="0">Select Language</option>
                        <option value="1">Bangla</option>
                        <option value="2">English</option>
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="col mb-3">
                    <label for="nameBasic" class="form-label">Evaluation Month</label>
                    <select  class="form-select eval_language" name="eval_month" required>
                        <option value="0">Select Month</option>
                        @foreach($month as $key=>$value)
                        <option value="{{$key}}">{{$value}}</option>
                        @endforeach
                    </select>
                  </div>
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


  </div>
  <script type="text/javascript">
    $(document).ready(function() {
      
        new DataTable('#scale_list');

        $(document).on('change','.school_info',function(e) {
          var school_id = $(this).val();
          console.log(school_id);
            $.ajax({
                url: "{{ url('get-school-types/') }}"+ '/'+ school_id,
                type: "GET",
                dataType: "html",
                success: function(data){
                    $('.school_type').empty();
                    $('.school_type').append(data);
                }
            });
        });
    });
  </script>
@endsection