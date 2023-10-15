@extends('admin.layouts.app')

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">CQAMS Evaluation Question</h4>

      <div class="row">
        <!-- Basic Layout -->
        <div class="col-xxl">
          <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
            </div>
            <div class="card-body">
              <form method="POST" action="{{url('/')}}/admin/tqams/submit">
              @csrf
              @if(isset($eval_data) && ($eval_data != ''))
                @php
                  $questions_data = json_decode($eval_data->answer, true);
                @endphp
              @endif
              <div class="alert alert-dark mb-0" role="alert" style="text-align: center; font-size: 24px;">{{ $school_title }}</div>
              @foreach($category as $row=>$val)
                <div class="question-list" style="margin-top: 30px;">
                    <h4 style="text-align:center; background-color: rgba(105,108,255,.16)!important; color: #696cff; border-radius: 0.375rem; padding: 10px;" class="card-header" >{{$row+1}}. {{$val->criteria_name_en}}</h4>
                    @foreach($subCatgory[$val->evalution_criteria_row_id] as $srow=>$sval)
                        <div class="category">
                        <h5 style="font-weight:600; font-size:18px;" class="card-header">{{$row+1}}.{{$srow+1}}  {{$sval->criteria_name_en}}</h5>
                        <ol style="list-style-type:lower-alpha">

                            @foreach($question[$sval->evalution_sub_criteria_row_id] as $qrow=>$qval)

                                <div class="question" style="margin-top: 10px;">
                                <h6 style="margin-left:20px;color:black; font-size:16px;"><li style="font-weight: 500;">{{$qval->field_name_en}}</li></h6>
                                <div class="form-group" style="margin-left:40px; color:black;">
                                    <div class="controls">
                                        @php $checked = ''; @endphp
                                        @foreach($scaleList[$qval->evaluation_field_row_id] as $scales=>$scale)
                                          @if(isset($eval_data) && ($eval_data != ''))
                                            @if(array_key_exists($qval->evaluation_field_row_id, $questions_data))
                                              @if($questions_data[$qval->evaluation_field_row_id] == $scale->scale_point)
                                                @php $checked = 'checked'; @endphp
                                              @else
                                                @php $checked = ''; @endphp
                                              @endif
                                            @endif
                                          @endif
                                            <label style="margin: 0px 10px;"><input type="radio" name="answer[{{$qval->evaluation_field_row_id}}]" value="{{$scale->scale_point}}" {{$checked}} required> {{$scale->scale_name}}</label>
                                        @endforeach
                                    </div>
                                </div>
                                </div>
                            @endforeach
                        </ol>
                        </div>
                    @endforeach
                </div>
              @endforeach
              <div class="mb-4" style="margin-top:30px;">
                <label for="exampleFormControlTextarea1" class="form-label" style="font-size: 1rem;">Feedback/Comments</label>
                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="feedback">@if(isset($eval_data) && ($eval_data != '')) {{ $eval_data->feedback }} @endif</textarea>
              </div>
              <div class="row justify-content-end">
                <div class="col-sm-12">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </div>
              <input type="hidden" name="eval_id" value="{{ $cse_id }}">
              <input type="hidden" name="is_update" value="@if(isset($eval_data) && ($eval_data != '')) 1 @else 0 @endif">
              </form>
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