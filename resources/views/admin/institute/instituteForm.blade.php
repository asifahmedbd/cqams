@extends('admin.layouts.app')

@section('content')
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <style>
    .select2-container{
      width: 480px !important;
    }
    .select2-hidden-accessible{
      border-color: #c7cdd4 !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
      border: 1px solid #c7cdd4 !important;
    }
  </style>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">@if(isset($school)) Edit @else Add @endif Institution Info</h4>

    <form action="{{url('/')}}/admin/insitution/save" method="POST">
      @csrf
      @if(isset($school)) <input type="hidden" name="osid" value="{{$school->school_id}}"> @endif
      <!-- Basic Layout & Basic with Icons -->
      <div class="row">
        <!-- Basic Layout -->
        <div class="col-xxl">
          <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h5 class="mb-0">Basic Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="col-sm-6 col-form-label" for="institute-name">Institution's Name <span class="text-danger">*</span></label>
                    <div class="col-sm-12">
                      <input type="text" class="form-control" id="institute-name" name="name_en" placeholder="ex: Asiya Bari Ideal School" @if(isset($school)) value="{{$school->school_name}}" @endif required />
                    </div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="col-sm-6 col-form-label" for="basic-default-company">প্রতিষ্ঠানের নাম
                     <span class="text-danger">*</span></label>
                    <div class="col-sm-12">
                      <input
                        type="text"
                        class="form-control"
                        required data-validation-required-message="This field is required"
                        id="name_bn"
                        name="name_bn"
                        @if(isset($school)) value="{{$school->school_name_bn}}" @endif
                        placeholder="ex: আসিয়া বারি আদর্শ বিদ্যালয়" />
                    </div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="col-sm-6 col-form-label" for="basic-default-name">Slogan</label>
                    <div class="col-sm-12">
                      <input type="text" class="form-control" id="slogan_en" name="slogan_en" @if(isset($school)) value="{{$school->school_slogan}}" @endif placeholder="ex: For a Better Future" />
                    </div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="col-sm-6 col-form-label" for="basic-default-name">স্লোগান</label>
                    <div class="col-sm-12">
                      <input type="text" class="form-control" id="slogan_bn" name="slogan_bn" @if(isset($school)) value="{{$school->school_slogan_bangla}}" @endif placeholder="ex: উন্নত ভবিষ্যতের লক্ষ্যে" />
                    </div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="col-sm-6 col-form-label" for="basic-default-name">EIN Number</label>
                    <div class="col-sm-12">
                      <input type="number" class="form-control" id="eiin" name="eiin" @if(isset($school)) value="{{$school->school_eiin_id}}" @endif placeholder="ex: 0211568" />
                    </div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="col-sm-6 col-form-label" for="basic-default-name">Establishment</label>
                    <div class="col-sm-12">
                      <input type="date" class="form-control" id="es_date" name="es_date" @if(isset($school)) value="{{$school->school_establishment_date}}" @endif />
                    </div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="col-sm-6 col-form-label" for="basic-default-name">Institution Type</label>
                    <div class="col-sm-12">
                      <select class="js-example-basic-multiple" name="inst_type[]" multiple="multiple">
                        <option value="">Select type</option>
                          @foreach($type as $types=>$typ)
                              @if(isset($school->type))
                                @php
                                  $inst_types = json_decode($school->type, true);
                                @endphp
                              @endif
                          <option @if(isset($school) && (in_array($typ->id, $inst_types))) selected @endif value="{{$typ->id}}">{{$typ->name}}</option>
                          @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="exampleFormControlSelect1" class="form-label">Education Board</label>
                      <select class="form-select" id="exampleFormControlSelect1" aria-label="Default select example" name="school_board">
                        <option value="" selected>Select Education Board</option>
                        @foreach($eduBoard as $boards=>$board)
                        <option value="{{$board->board_row_id}}" @if(isset($school) && $school->school_board==$board->board_row_id) selected @endif>{{$board->board_title}}</option>
                        @endforeach
                      </select>
                  </div>
                  
                </div>
            </div>
          </div>
        </div>


        <div class="col-xxl">
          <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h5 class="mb-0">Address & Contact Info</h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-3 mb-3">
                  <label for="exampleFormControlSelect1" class="form-label">Division (বিভাগ) <span class="text-danger">*</span></label>
                    <select class="form-select" name="division" id="division" required>
                      <option value="" selected>Select Division</option>
                      @foreach($division as $items=>$item)
                      <option value="{{$item->id}}" @if(isset($school) && $school->division_id==$item->id) selected @endif>{{$item->name}}</option>
                      @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="exampleFormControlSelect1" class="form-label">District (জেলা) <span class="text-danger">*</span></label>
                    <select class="form-select" name="district" id="district" required>
                      <option value="">Select District</option>
                      @if(isset($school))
                          @foreach($district as $row=>$val)
                          <option value="{{$val->id}}" @if($school->district_id==$val->id) selected @endif>{{$val->full_name}}</option>
                          @endforeach
                      @endif
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="exampleFormControlSelect1" class="form-label">Thana/Upazila(থানা/উপজেলা) <span class="text-danger">*</span></label>
                    <select class="form-select" name="upazila" id="upazila" required>
                      <option value="">Select Thana/Upazila</option>
                      @if(isset($school))
                          @foreach($upazila as $row=>$val)
                          <option value="{{$val->id}}" @if($school->upazila_id==$val->id) selected @endif >{{$val->full_name}}</option>
                          @endforeach
                      @endif
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="exampleFormControlSelect1" class="form-label">Postal Code</label>
                    <div class="col-sm-12">
                      <input type="number" class="form-control" name="postal_code" @if(isset($school)) value="{{$school->post_code}}" @endif />
                    </div>
                </div>
                <div class="mb-3">
                  <label for="exampleFormControlTextarea1" class="form-label">Full Address</label>
                  <textarea class="form-control" name="full_address" id="" cols="30" rows="3" placeholder="ex: 432/A Dhanmondi Dhaka">@if(isset($school)) {{$school->school_address}} @endif</textarea>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="exampleFormControlSelect1" class="form-label">Mobile <span class="text-danger">*</span></label>
                    <div class="col-sm-12">
                      <input type="tel" placeholder="+880-xxxx-xxx-xxx" class="form-control" name="mobile" @if(isset($school)) value="{{$school->school_mobile_no}}" @endif>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="exampleFormControlSelect1" class="form-label">Email <span class="text-danger">*</span></label>
                    <div class="col-sm-12">
                      <input type="email" placeholder="ex: abis@gmail.com" class="form-control" name="email" @if(isset($school)) value="{{$school->school_email}}" @endif>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="exampleFormControlSelect1" class="form-label">Telephone </label>
                    <div class="col-sm-12">
                      <input type="tel" placeholder="+02-xxx-xxx" class="form-control" name="tel" @if(isset($school)) value="{{$school->school_phone_no}}" @endif>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="exampleFormControlSelect1" class="form-label">Website </label>
                    <div class="col-sm-12">
                      <input type="text" placeholder="https://www.example.com" class="form-control" name="website" @if(isset($school)) value="{{$school->website}}" @endif>
                    </div>
                </div>

                <div class="row justify-content-end">
                    <div class="col-sm-12">
                      <button type="submit" class="btn btn-primary">@if(isset($school)) Update @else Submit @endif</button>
                    </div>
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
  <script type="text/javascript">
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2();
        $('#division').change(function(){
            var division = $(this).val();
            $.ajax({
                url: "{{ url('getDistrict/') }}"+ '/'+ division,
                type: "GET",
                dataType: "html",
                success: function(data){
                    $('#district').empty();
                    $('#district').append(data);
                }
            });
        })

        $('#district').trigger('change');

        $('#district').change(function(){
            var district = $(this).val();
            $.ajax({
                url: "{{ url('getUpazila/') }}"+ '/'+ district,
                type: "GET",
                dataType: "html",
                success: function(data){
                    $('#upazila').empty();
                    $('#upazila').append(data);
                }
            });
        })
    });
  </script>
@endsection