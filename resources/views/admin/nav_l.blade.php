
<nav class="navbar-default navbar-static-side" role="navigation">
	<div class="sidebar-collapse">
		<ul class="nav metismenu" id="side-menu">
			<li class="nav-header">
				<div class="dropdown profile-element">
 					<span> 
						<img alt="image" class="img-circle" src="/img/profile_small.jpg" />
					</span>
<!-- 					<a data-toggle="dropdown" class="dropdown-toggle" href="#"> -->
<!-- 						<span class="clear">  -->
<!-- 							<span class="block m-t-xs">  -->
<!-- 								<strong class="font-bold"></strong> -->
<!-- 							</span>  -->
<!-- 							<span class="text-muted text-xs block"><b class="caret"></b></span> -->
<!-- 						</span> -->
<!-- 					</a> -->
<!-- 					<ul class="dropdown-menu animated fadeInRight m-t-xs"> -->
<!-- 						<li><a href="profile.html">Profile</a></li> -->
<!-- 						<li><a href="contacts.html">Contacts</a></li> -->
<!-- 						<li><a href="mailbox.html">Mailbox</a></li> -->
<!-- 						<li class="divider"></li> -->
<!-- 						<li><a href="Logout">Logout</a></li> -->
<!-- 					</ul> -->
				</div>
				<div class="logo-element">IN+</div>
			</li>
			<li @if ($routeName == 'admin')  class="active" @endif>
				<a href="{{ url('/admin') }}"><i class="fa fa-th-large"></i>
			<span class="nav-label">主页</span></a></li>
			
			<li @if ($routeName == 'authorize')  class="active" @endif>
				<a href="{{ url('/admin/authorize') }}"><i class="fa fa-magic"></i>
			<span class="nav-label">微博授权</span></a></li>
				
			<li @if ($groupName == 'weibo')  class="active" @endif>
				<a href="#"  ><i class="fa fa-table"></i> <span class="nav-label">数据统计</span><span class="fa arrow"></span></a>
				<ul class="nav nav-second-level collapse">
					<li><a href="">功能说明</a></li>
					<li @if ($routeName == 'weibo')  class="active" @endif><a href="{{ url('/admin/weibo') }}">统计任务</a></li>
					<li @if ($routeName == 'users')  class="active" @endif><a href="{{ url('/admin/users') }}">用户统计</a></li>
				</ul>
			</li>
			<li @if ($groupName == 'complete')  class="active" @endif>
				<a href="#"><i class="fa fa-sitemap"></i> <span class="nav-label">综合分析</span><span class="fa arrow"></span></a>
				<ul class="nav nav-second-level collapse">
					<li><a href="#">功能说明</a></li>
					<li @if ($routeName == 'index')  class="active" @endif><a href="{{ url('/admin/complete') }}">分析用户</a></li>
					<li><a href="#">Second Level Item</a></li>				
<!-- 					<li><a href="#">Third Level <span class="fa arrow"></span></a> -->
<!-- 						<ul class="nav nav-third-level"> -->
<!-- 							<li><a href="#">Third Level Item</a></li> -->
<!-- 							<li><a href="#">Third Level Item</a></li> -->
<!-- 							<li><a href="#">Third Level Item</a></li> -->
<!-- 						</ul></li> -->
				</ul>
			</li>


		</ul>

	</div>
</nav>

