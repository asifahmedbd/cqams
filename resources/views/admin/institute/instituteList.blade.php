@extends('admin.layouts.app')

@section('content')
  
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">Institution Lists</h4>

      <div class="row">
        <!-- Basic Layout -->
        <div class="col-xxl">
          <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Inst. Name</th>
                        <th>Location</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      @foreach($school as $items=>$item)
                        @php
                          $inst_types = json_decode($item->type);
                        @endphp
                      <tr>
                        <td>{{$item->school_id}}</td>
                        <td>{{$item->school_name}}</td>
                        <td>@if(isset($item->upazila)){{$item->upazila->full_name}}@endif</td>
                        <td>
                          @foreach($inst_types as $key=>$tid)
                            <span class="badge bg-primary">{{ $instTypes[$tid]->name }}</span><br>
                          @endforeach
                        </td>
                        <td>
                          @if($item->is_active==1)
                          <span class="badge bg-label-primary me-1">Active</span>
                          @else
                          <span class="badge bg-label-danger me-1">Inactive</span>
                          @endif
                        </td>
                        <td>
                          <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                              <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                              <a class="dropdown-item" href="{{url('/')}}/admin/edit/{{$item->school_id}}/institute"
                                ><i class="bx bx-edit-alt me-1"></i> Edit</a
                              >
                              <a class="dropdown-item" href="javascript:void(0);"
                                ><i class="bx bx-trash me-1"></i> Delete</a
                              >
                            </div>
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
  </div>
  <script type="text/javascript">
    $(document).ready(function() {
        
    });
  </script>
@endsection