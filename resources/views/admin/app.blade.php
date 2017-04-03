<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>My CMS</title>

    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/font-awesome/css/font-awesome.css" rel="stylesheet">
    
    <link href="/css/animate.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">	
	
	@yield('style')

</head>

<body>
	<div id="wrapper">
		@include('admin/nav_l')
		<div id="page-wrapper" class="gray-bg dashbard-1">
			@include('admin/nav_r') 
			
			@yield('content')

			<div class="row">
				<div class="col-lg-12">
					<div class="footer">
						<div class="pull-right">
						</div>
						<div>
							<strong>Copyright</strong> daweilang &copy; 2016-2017
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Mainly scripts -->
    <script src="/js/jquery-2.1.1.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="/js/plugins/jeditable/jquery.jeditable.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="/js/inspinia.js"></script>
    <script src="/js/plugins/pace/pace.min.js"></script>
    
	@yield('javascript')

</body>
</html>
