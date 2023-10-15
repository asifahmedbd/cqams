@extends('admin.layouts.app')

@section('content')
  <link href="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.css" rel="stylesheet">
  <script src="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.js"></script>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">Question Categories</h4>

      <div class="row">
        <!-- Basic Layout -->
        <div class="col-xxl">
          <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
            </div>
            <div class="card-body">
                <div class="col-md-12 bg-light mb-3" style="text-align: right;">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#basicModal">Add New Category</button>
                </div>
                
                <div class="table-responsive text-nowrap">
                  <table class="table table-bordered" id="question_categories">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Category Title (En)</th>                 
                        <th>Categort Title (Bn)</th>
                        <th style="text-align:center;">Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                          <?php $i=1; ?>
                          @foreach($evaluation_fields as $row)
                            <tr>
                              <td>{{$i}}</td>
                              <td>{{$row->criteria_name_en}}</td>
                              <td>{{$row->criteria_name_bn}}</td>
                              <td style="text-align:center;">
                                <div class="btn-group btn-group-xs btn-group-solid" >
                                  <button id="editField" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#basicModal" sid="{{$row->id}}" name="{{$row->scale_name}}" name_bn="{{$row->scale_name_bn}}" options="{{$row->scale_point}}">Edit</button>
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
        <div class="modal-dialog" role="document">
          <form action="{{ url('/') }}/admin/tqams/storeCriteriaMultiple" method="POST" class="form-horizontal">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Add Category</h5>
                <button
                  type="button"
                  class="btn-close"
                  data-bs-dismiss="modal"
                  aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col mb-3">
                    <label for="nameBasic" class="form-label">Category(English)</label>
                    <input type="text" class="form-control title_en" name="criteria_title_en" placeholder="Enter Evaluation Criteria (en)" autocomplete="off" required>
                  </div>
                </div>
                <div class="row">
                  <div class="col mb-3">
                    <label for="nameBasic" class="form-label">Category(Bangla)</label>
                    <input type="text" class="form-control title_bn" name="criteria_title_bn" placeholder="Enter Evaluation Criteria (bn)" autocomplete="off" required>
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

        new DataTable('#question_categories');

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