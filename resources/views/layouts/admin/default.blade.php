<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>
    @hasSection('page_title')
      @yield('page_title') | {{ Settings::getOrgName() }}
    @else
      {{ Settings::getOrgName() }} Admin
    @endif
  </title>

  <link rel="icon" type="image/png" sizes="32x32" href="{{ Settings::getOrgFavicon() }}">
  
  <!-- Bootstrap Core CSS -->
  <link href="/css/admin/bootstrap.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link href="/css/admin/sb-admin.css" rel="stylesheet">

  <!-- Morris Charts CSS -->
  <link href="/css/admin/plugins/morris.css" rel="stylesheet">

  <!-- Custom Fonts -->
  <link href="/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Jquery Core JavaScript -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

  <!-- Bootstrap Core JavaScript -->
  <script src="/javascripts/admin/bootstrap.min.js"></script>

  <!-- Morris Charts JavaScript -->
  <script src="/javascripts/admin/plugins/morris/raphael.min.js"></script>
  <script src="/javascripts/admin/plugins/morris/morris.min.js"></script>
  <script src="/javascripts/admin/plugins/morris/morris-data.js"></script>

  <!-- Auto Complete -->
  <script src="/javascripts/admin/jquery.autocomplete.min.js"></script>

  <script>

    function ConfirmDelete()
    {
    var x = confirm("Are you sure you want to delete?");
    if (x)
      return true;
    else
      return false;
    }
    function ConfirmSubmit()
    {
    var x = confirm("Are you sure you want to submit?");
    if (x)
      return true;
    else
      return false;
    }

  </script>

</head>
<body>
  <div id="wrapper">
    @include('layouts._partials._admin.navigation')
    <div id="page-wrapper">
      <div class="container-fluid">
          <div class='row'>
            @foreach (['danger', 'warning', 'success', 'info'] as $msg)
              @if(Session::has('alert-' . $msg))
                <p class="alert alert-{{ $msg }}">
                  <b>{{ Session::get('alert-' . $msg) }}</b> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </p>
              @endif
            @endforeach
          </div>
        @yield('content')

      </div>
    </div>    
  </div>
</body>
</html>
