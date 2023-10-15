@extends('admin.layouts.app')

@section('content')
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="https://code.highcharts.com/modules/data.js"></script>
  <script src="https://code.highcharts.com/modules/drilldown.js"></script>
  <script src="https://code.highcharts.com/modules/exporting.js"></script>
  <script src="https://code.highcharts.com/modules/export-data.js"></script>
  <script src="https://code.highcharts.com/modules/accessibility.js"></script>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">CQAMS REPORT</h4>

      <div class="row">
        <!-- Basic Layout -->
        <div class="col-xxl">
          <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
            </div>
            <div class="card-body">

              <div class="row">
                  <div class="col-md-3 mb-3">
                    <label for="exampleFormControlSelect1" class="form-label">Academic Session</label>
                      <select  class="form-select academi_session" name="academi_session" required>
                        <option value="0">Select Session</option>
                        @foreach($all_session as $sdata)
                        <option value="{{$sdata->academic_session_row_id}}">{{$sdata->academic_session_year}}-{{ $month[$sdata->month] }}</option>
                        @endforeach   
                    </select>
                  </div>
                  <div class="col-md-3 mb-3">
                    <label for="nameBasic" class="form-label">Select School</label>
                    <select  class="form-select school_info" name="school" required>
                      <option value="0">Select School</option>
                      @foreach($schools as $scdata)
                      <option value="{{$scdata->school_id}}">{{$scdata->school_name}}</option>
                      @endforeach   
                    </select>
                  </div>

                  <div class="col-md-3 mb-3" >
                    <label for="nameBasic" class="form-label">Select Evaluation</label>
                    <select  class="form-select evaluation_data" name="evaluation_data" required>

                    </select>
                  </div>

                  <div class="col-md-3 mb-3" style="margin-top: 25px;">
                    <button type="button" class="btn btn-primary generate_report">Load</button>
                  </div>

              </div>


              <div class="row">
                <div class="show_term_wise_report"></div>
              </div>

            </div>
          </div>
        </div>
      </div>


  </div>
  <script type="text/javascript">
    $(document).ready(function() {

      $(document).on('change','.school_info',function(e) {
        var academic_session = $('.academi_session').val();
        var school_id = $('.school_info').val();

          $.ajax({
              url: "{{ url('get-evaluation-data/') }}"+ '/'+ academic_session+'/'+school_id,
              type: "GET",
              dataType: "html",
              success: function(data){
                  $('.evaluation_data').empty();
                  $('.evaluation_data').append(data);
              }
          });
      });

      $('.generate_report').on('click',function(){
          $('.show_term_wise_report').empty(); 
          var academic_session = $('.academi_session').val();
          var school_id = $('.school_info').val();
          var eval_id = $('.evaluation_data').val();

        $('#download_result').attr('href', "{{ url('/school-admin/tqams-report-ajax/') }}"+ '/'+ academic_session + '/'+ 1)
        $.ajax({
            url: "{{ url('/admin/tqams-report-ajax/') }}"+ '/'+ academic_session+ '/'+school_id + '/'+ eval_id + '/'+ 0,
            type: "GET",
            dataType: "html",
            success: function(data){
              $('.download_report_card').show();
                $('.show_term_wise_report').append(data);
            }
        });

      });



    });
  </script>
@endsection