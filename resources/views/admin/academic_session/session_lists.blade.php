@extends('admin.layouts.app')

@section('content')
  <link href="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.css" rel="stylesheet">
  <script src="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.js"></script>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">Academic Session Lists</h4>

      <div class="row">
        <!-- Basic Layout -->
        <div class="col-xxl">
          <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
            </div>
            <div class="card-body">
                <div class="col-md-12 bg-light mb-3" style="text-align: right;">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#basicModal">Add Session</button>
                </div>
                
                <div class="table-responsive text-nowrap">
                  <table class="table table-bordered" id="scale_list">
                    <thead>
                      <tr>
                        <th>Name</th>                  
                        <th>Year</th>                  
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th style="text-align:center;">Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                          @foreach($all_session as $row)
                            @php 
                              $sdate=date_create($row->start_date);
                              $edate=date_create($row->end_date); 
                            @endphp
                            <tr>
                              <td>{{ $row->academic_session_year}}-{{$month[$row->month]}}</td>
                              <td>{{ $row->academic_session_year }}</td>
                              <td>{{ date_format($sdate,"F d, Y") }}</td>
                              <td>{{ date_format($edate,"F d, Y") }}</td>
                              <td>
                                @if($row->is_active==1)                 
                                  <span class="badge bg-label-primary me-1">Active</span>
                                  @else
                                  <span class="badge badge-pill badge-danger">Inactive</span>
                                @endif
                              </td>
                              <td style="text-align:center;">
                                <div class="btn-group btn-group-xs btn-group-solid" >
                                  <button id="editField" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#basicModal" sid="{{$row->id}}">Edit</button>
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
          <form action="{{ url('/') }}/admin/storeAcademicSession" method="POST" class="form-horizontal">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Add New Session</h5>
                <button
                  type="button"
                  class="btn-close"
                  data-bs-dismiss="modal"
                  aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col mb-3">
                    <label for="nameBasic" class="form-label">Select Year</label>
                    <select class="form-select" name="academic_session_year" id="" required>
                        <option value="">Select Year</option>
                        {{ $last= date('Y')+10 }}
                        {{ $now = date('Y') }}

                        @for ($i = $now; $i <= $last; $i++)
                          <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="col mb-3">
                    <label for="nameBasic" class="form-label">Session Month</label>
                    <select class="form-select" name="month" id="" required>
                      <option value="">Select Month</option>
                        @foreach($month as $months=>$month)
                        <option value="{{$months}}">{{$month}}</option>
                        @endforeach
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="col mb-3">
                    <label for="startDate" class="form-label">Start Date</label>
                    <input type="date" class="form-control" name="start_date" id="startDate" placeholder="Point of Scale" required>
                  </div>
                </div>
                <div class="row">
                  <div class="col mb-3">
                    <label for="endDate" class="form-label">End Date</label>
                    <input type="date" class="form-control" name="end_date" id="endDate" placeholder="Point of Scale" required>
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

        $(document).on('click','#editField',function(e) {
          var option = $(this).attr('options');
          var id = $(this).attr('sid');
          var name = $(this).attr('name');
          var name_bn = $(this).attr('name_bn');
          $('.up').remove();
          console.log(name);
          $('#name_set').val(name);
          $('#name_set_bn').val(name_bn);
          $('#scales').val(option);
          $(".submitForm").after('<input type="hidden" class="up" name="sid" value="'+id+'" >');
        });
    });
  </script>
@endsection