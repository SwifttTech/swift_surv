<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{env('APP_NAME')}} - Most recent responses</title>
    <!-- HTML5 Shim and Respond.js IE11 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 11]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
      <![endif]-->
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="Phoenixcoded" />
    <!-- Favicon icon -->
    <link rel="icon" href="{{url('')}}assets/images/favicon.png" type="image/x-icon">

    <!-- vendor css -->
    <link rel="stylesheet" href="{{url('')}}assets/css/style.css">
    <link href="{{url('')}}assets/libs/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{ url('') }}assets/css/weza.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">

  <script src="http://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css"></script>

    
    

</head>
<body class="">
  <!-- [ Pre-loader ] start -->
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>
  <!-- [ Pre-loader ] End -->
  <!-- [ navigation menu ] start -->
@include('includes/sidebar')
@include('includes/header')
  
  

<!-- [ Main Content ] start -->
<div class="pcoded-main-container">
    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Dashboard Analytics</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="#!">Dashboard Analytics</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-lg-7 col-md-12">
                <!-- support-section start -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card support-bar overflow-hidden">
                            <div class="card-body pb-0">
                                <h2 class="m-0">{{$ussdCount[0]->count}}</h2>
                                <span class="text-c-blue">USSD Sessions</span>
                                <p class="mb-3 mt-3">Total number of USSD Sessions.</p>
                            </div>
                            <div id="support-chart"></div>
                            <div class="card-footer bg-primary text-white">
                                <div class="row text-center">
                                    <div class="col">
                                        <h4 class="m-0 text-white">{{$ussdCountToday[0]->count}}</h4>
                                        <span>Today</span>
                                    </div>
                                    <div class="col">
                                        <h4 class="m-0 text-white">{{$ussdCountMonth[0]->count}}</h4>
                                        <span>This Month</span>
                                    </div>
                                    <div class="col">
                                        <h4 class="m-0 text-white">{{$ussdCountYear[0]->count}}</h4>
                                        <span>This Year</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card support-bar overflow-hidden">
                            <div class="card-body pb-0">
                                <h2 class="m-0">{{$smsCount[0]->count}}</h2>
                                <span class="text-c-green">All SMS Responses</span>
                                <p class="mb-3 mt-3">Total number of sms survey responses.</p>
                            </div>
                            <div id="support-chart1"></div>
                            <div class="card-footer bg-success text-white">
                                <div class="row text-center">
                                    <div class="col">
                                        <h4 class="m-0 text-white">{{$smsCountToday[0]->count}}</h4>
                                        <span>Today</span>
                                    </div>
                                    <div class="col">
                                        <h4 class="m-0 text-white">{{$smsCountMonth[0]->count}}</h4>
                                        <span>This Month</span>
                                    </div>
                                    <div class="col">
                                        <h4 class="m-0 text-white">{{$smsCountYear[0]->count}}</h4>
                                        <span>This Year</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- support-section end -->
            </div>
            <div class="col-lg-5 col-md-12">
                @if($user->email == "joglikibii@gmail.com" || $user->email == "kiruimichael@gmail.com")
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h4 class="text-c-yellow">Kes: {{$balance[0]->balance}}/-</h4>
                                <h6 class="text-muted m-b-0">Balance</h6>
                            </div>
                            <div class="col-4 text-right">
                                <i class="feather icon-coins f-28"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
                <!-- page statustic card start -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <h4 class="text-c-yellow">{{$incompleteSms[0]->count}}</h4>
                                        <h6 class="text-muted m-b-0">Incomplete</h6>
                                    </div>
                                    <div class="col-4 text-right">
                                        <i class="feather icon-alert-triangle f-28"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-c-yellow">
                                <div class="row align-items-center">
                                    <div class="col-9">
                                        <p class="text-white m-b-0">SMS</p>
                                    </div>
                                    <div class="col-3 text-right">
                                        <i class="feather icon-trending-up text-white f-16"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <h4 class="text-c-green">{{$completeSms[0]->count}}</h4>
                                        <h6 class="text-muted m-b-0">Complete</h6>
                                    </div>
                                    <div class="col-4 text-right">
                                        <i class="feather icon-check f-28"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-c-green">
                                <div class="row align-items-center">
                                    <div class="col-9">
                                        <p class="text-white m-b-0">SMS</p>
                                    </div>
                                    <div class="col-3 text-right">
                                        <i class="feather icon-trending-up text-white f-16"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                   
                    
                </div>
                <!-- page statustic card end -->
            </div>
            <!-- prject ,team member start -->
            <div class="col-xl-12 col-md-12">
                <div class="card table-card">
                    <div class="card-header">
                        <h5>Summary of Responses by Survey</h5>
                        <div class="card-header-right">
                            <div class="btn-group card-option">
                                <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="feather icon-more-horizontal"></i>
                                </button>
                                <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                                    <li class="dropdown-item full-card"><a href="#!"><span><i class="feather icon-maximize"></i> maximize</span><span style="display:none"><i class="feather icon-minimize"></i> Restore</span></a></li>
                                    <li class="dropdown-item minimize-card"><a href="#!"><span><i class="feather icon-minus"></i> collapse</span><span style="display:none"><i class="feather icon-plus"></i> expand</span></a></li>
                                    <li class="dropdown-item reload-card"><a href="#!"><i class="feather icon-refresh-cw"></i> reload</a></li>
                                    <li class="dropdown-item close-card"><a href="#!"><i class="feather icon-trash"></i> remove</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" class="display" style="width:100%">
                                <thead>
                                        <th>#</th>
                                        <th>Survey</th>
                                        <th>Complete</th>
                                        <th>Incomplete</th>
                                        <th>Total No. of Responses</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($surveys as $key => $survey)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td><a href="{{url('Surveys@responses'),$survey->id}}">{{$survey->name}}</a></td>
                                        <td><b>{{number_format($survey->complete[0]->count)}}</b> ({{number_format($survey->complete[0]->count/$survey->total[0]->count*100,2)}}%)</td>
                                        <td><b>{{number_format($survey->incomplete[0]->count)}}</b> ({{number_format($survey->incomplete[0]->count/$survey->total[0]->count*100,2)}}%)</td>
                                        <td><b>{{number_format($survey->total[0]->count)}}</b> (100%)</td>
                                        <td class="text-center">
                                            @if($survey->status == '1')                                        
                                            <label class="badge badge-light-success">Active</label>
                                            @else
                                            <label class="badge badge-light-danger">Inactive</label>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--<div class="col-xl-12 col-md-12">-->
            <!--    <div class="card table-card">-->
            <!--        <div class="card-header">-->
            <!--            <h5>20 Most recent responses</h5>-->
            <!--            <div class="card-header-right">-->
            <!--                <div class="btn-group card-option">-->
            <!--                    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">-->
            <!--                        <i class="feather icon-more-horizontal"></i>-->
            <!--                    </button>-->
            <!--                    <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">-->
            <!--                        <li class="dropdown-item full-card"><a href="#!"><span><i class="feather icon-maximize"></i> maximize</span><span style="display:none"><i class="feather icon-minimize"></i> Restore</span></a></li>-->
            <!--                        <li class="dropdown-item minimize-card"><a href="#!"><span><i class="feather icon-minus"></i> collapse</span><span style="display:none"><i class="feather icon-plus"></i> expand</span></a></li>-->
            <!--                        <li class="dropdown-item reload-card"><a href="#!"><i class="feather icon-refresh-cw"></i> reload</a></li>-->
            <!--                        <li class="dropdown-item close-card"><a href="#!"><i class="feather icon-trash"></i> remove</a></li>-->
            <!--                    </ul>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--        <div class="card-body p-0">-->
            <!--            <div class="table-responsive">-->
            <!--                <table class="table table-hover mb-0" id="dataTable" class="display" style="width:100%">-->
            <!--                    <thead>-->
            <!--                            <th>#</th>-->
            <!--                            <th>Survey</th>-->
            <!--                            <th>Phone</th>-->
            <!--                            <th>Pressing Issue</th>-->
            <!--                            <th>Governor</th>-->
            <!--                            <th>Deputy Governor</th>-->
            <!--                            <th>President</th>-->
            <!--                            <th>Party</th>-->
            <!--                            <th>MCA</th>-->
            <!--                            <th>Created at</th>-->
            <!--                            <th class="text-right">Status</th>-->
            <!--                        </tr>-->
            <!--                    </thead>-->
            <!--                    <tbody>-->
            <!--                        @foreach($responses as $key => $response)-->
            <!--                        <tr>-->
            <!--                            <td>{{$key+1}}</td>-->
            <!--                            <td>-->
            <!--                                @if($response->campaign_id == 2443)-->
            <!--                                Turkana County-->
            <!--                                @elseif($response->campaign_id == 2444)-->
            <!--                                Kwale County-->
            <!--                                @elseif($response->campaign_id == 2090)-->
            <!--                                Taita Taveta-->
            <!--                                @endif-->
            <!--                            </td>-->
            <!--                            <td>{{$response->msisdn}}</td>-->
            <!--                            <td>{{$response->pressing_issue}}</td>-->
            <!--                            <td>{{$response->governor}}</td>-->
            <!--                            <td>{{$response->deputy_governor}}</td>-->
            <!--                            <td>{{$response->presidency}}</td>-->
            <!--                            <td>{{$response->party}}</td>-->
            <!--                            <td>{{$response->mca}}</td>-->
            <!--                            <td>{{$response->created_at}}</td>-->

            <!--                            @if($response->status == '1')-->
            <!--                            <td class="text-right"><label class="badge badge-light-success">Complete</label></td>-->
            <!--                            @else-->
            <!--                            <td class="text-right"><label class="badge badge-light-danger">Incomplete</label></td>-->
            <!--                            @endif-->
            <!--                        </tr>-->
            <!--                        @endforeach-->
            <!--                    </tbody>-->
            <!--                </table>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->

            <!-- Latest Customers end -->
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>

    <script src="{{url('')}}assets/js/vendor-all.min.js"></script>

  <script src="{{url('')}}assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="{{url('')}}assets/libs/jquery-easing/jquery.easing.min.js"></script>

  <script src="{{url('')}}assets/libs/datatables/jquery.dataTables.min.js"></script>
  <script src="{{url('')}}assets/libs/datatables/dataTables.bootstrap4.min.js"></script>

  <script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>


    <!-- Required Js -->
    <script src="{{url('')}}assets/js/plugins/bootstrap.min.js"></script>
    <script src="{{url('')}}assets/js/ripple.js"></script>
    <script src="{{url('')}}assets/js/pcoded.min.js"></script>

  <script src="{{url('')}}assets/js/demo/datatables-demo.js"></script>
<!-- Apex Chart -->
<!-- <script src="{{url('')}}assets/js/plugins/apexcharts.min.js"></script> -->


<!-- custom-chart js -->
<!-- <script src="{{url('')}}assets/js/pages/dashboard-main.js"></script> -->
</body>

</html>
