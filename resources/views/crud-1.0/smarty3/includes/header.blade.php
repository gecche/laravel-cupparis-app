
<!--
    HEADER

    .header-match-aside-primary
-->
<header id="header" class="d-flex align-items-center rounded-lg shadow-md">


    <!-- NAVBAR -->
    <div class="container-fluid position-relative">

        <nav class="navbar navbar-expand-lg navbar-light justify-content-lg-between justify-content-md-inherit">

            <div class="align-items-start">

                <!--
                    sidebar toggler
                -->
                <a href="#aside-main" class="btn-sidebar-toggle h-100 d-inline-block d-lg-none justify-content-center align-items-center p-2">
								<span class="group-icon">
									<i>
										<svg width="25" viewBox="0 0 20 20">
											<path d="M 19.9876 1.998 L -0.0108 1.998 L -0.0108 -0.0019 L 19.9876 -0.0019 L 19.9876 1.998 Z"></path>
											<path d="M 19.9876 7.9979 L -0.0108 7.9979 L -0.0108 5.9979 L 19.9876 5.9979 L 19.9876 7.9979 Z"></path>
											<path d="M 19.9876 13.9977 L -0.0108 13.9977 L -0.0108 11.9978 L 19.9876 11.9978 L 19.9876 13.9977 Z"></path>
											<path d="M 19.9876 19.9976 L -0.0108 19.9976 L -0.0108 17.9976 L 19.9876 17.9976 L 19.9876 19.9976 Z"></path>
										</svg>
									</i>

									<i>
										<svg width="25" viewBox="0 0 47.971 47.971">
											<path d="M28.228,23.986L47.092,5.122c1.172-1.171,1.172-3.071,0-4.242c-1.172-1.172-3.07-1.172-4.242,0L23.986,19.744L5.121,0.88c-1.172-1.172-3.07-1.172-4.242,0c-1.172,1.171-1.172,3.071,0,4.242l18.865,18.864L0.879,42.85c-1.172,1.171-1.172,3.071,0,4.242C1.465,47.677,2.233,47.97,3,47.97s1.535-0.293,2.121-0.879l18.865-18.864L42.85,47.091c0.586,0.586,1.354,0.879,2.121,0.879s1.535-0.293,2.121-0.879c1.172-1.171,1.172-3.071,0-4.242L28.228,23.986z"/>
										</svg>
									</i>
								</span>
                </a>


                <!--
                    Logo : height: 60px max
                    visibility : mobile only
                -->
                <a class="navbar-brand d-inline-block d-lg-none" href="/">
                    <span data-gfont="Sriracha" class="text-dark">
                        <img src="{!! Theme::url('images/ifc.png') !!}" width="55" height="40" alt="Abruzzo"> Abruzzo
                    </span>
                </a>


            </div>




            <!-- NAVIGATION -->
            <div class="collapse navbar-collapse" id="navbarMainNav">



                <!-- MOBILE MENU NAVBAR -->
                <div class="navbar-xs d-none">

                    <!-- mobile menu button : close -->
                    <button class="navbar-toggler pt-0" type="button" data-toggle="collapse" data-target="#navbarMainNav" aria-controls="navbarMainNav" aria-expanded="false" aria-label="Toggle navigation">
                        <svg width="20" viewBox="0 0 20 20">
                            <path d="M 20.7895 0.977 L 19.3752 -0.4364 L 10.081 8.8522 L 0.7869 -0.4364 L -0.6274 0.977 L 8.6668 10.2656 L -0.6274 19.5542 L 0.7869 20.9676 L 10.081 11.679 L 19.3752 20.9676 L 20.7895 19.5542 L 11.4953 10.2656 L 20.7895 0.977 Z"></path>
                        </svg>
                    </button>

                    <!--
                        Optional Mobile Menu Logo
                        Logo : height: 70px max
                    -->
                    <a class="navbar-brand" href="/">
                        <img src="{!! Theme::url('images/ifc.png') !!}" width="55" height="40" alt="..."> Abruzzo
                    </a>

                </div>
                <!-- /MOBILE MENU NAVBAR -->



                <!-- Dropdowns -->
                <ul class="navbar-nav align-items-center">


                </ul>
                <!-- /Dropdowns -->

            </div>
            <!-- /NAVIGATION -->




            <!-- OPTIONS -->
            <ul class="list-inline list-unstyled mb-0 d-flex align-items-end">

                @auth
                <!-- account -->
                <li class="list-inline-item ml--6 mr--6 dropdown">

                    <a href="#" id="dropdownAccountOptions" class="btn btn-sm btn-light dropdown-toggle btn-pill pl--12 pr--12" data-toggle="dropdown" aria-expanded="false" aria-haspopup="true">

									<span class="group-icon m-0">
										<!-- <i class="fi fi-user-female"></i> -->
										<i class="fi w--15 fi-user-male"></i>
										<i class="fi w--15 fi-close"></i>
									</span>

                        <span class="fs--14 d-none d-sm-inline-block font-weight-medium">{{ Auth::user()->name }}</span>
                    </a>


                    <!--

                        Dropdown Classes
                            .dropdown-menu-dark 		- dark dropdown (desktop only, will be white on mobile)
                            .dropdown-menu-hover 		- open on hover
                            .dropdown-menu-clean 		- no background color on hover
                            .dropdown-menu-invert 		- open dropdown in oposite direction (left|right, according to RTL|LTR)
                            .dropdown-click-ignore 		- keep dropdown open on inside click (useful on forms inside dropdown)

                            Dropdown prefix icon (optional, if enabled in variables.scss)
                                .prefix-link-icon .prefix-icon-dot 		- link prefix
                                .prefix-link-icon .prefix-icon-line 	- link prefix
                                .prefix-link-icon .prefix-icon-ico 		- link prefix
                                .prefix-link-icon .prefix-icon-arrow 	- link prefix

                                .prefix-icon-ignore 					- ignore, do not use on a specific link

                    -->
                    <div aria-labelledby="dropdownAccountOptions" class="prefix-link-icon prefix-icon-dot dropdown-menu dropdown-menu-clean dropdown-menu-navbar-autopos dropdown-menu-invert dropdown-click-ignore p-0 mt--18 fs--15 w--300">

                        <a href="/logout" class="prefix-icon-ignore dropdown-footer dropdown-custom-ignore font-weight-medium pt-3 pb-3">
                            <i class="fi fi-power float-start"></i>
                            Log Out
                        </a>
                        <a href="{{route('user.profile')}}" class="prefix-icon-ignore dropdown-footer dropdown-custom-ignore font-weight-medium pt-3 pb-3">
                            <i class="fi fi-user-male float-start"></i>
                            Profilo
                        </a>
                    </div>


                </li>
                @endauth

            </ul>
            <!-- /OPTIONS -->


        </nav>


        <!-- TOP NAVIGATION TOGGLER -->
        <button class="navbar-toggler h-auto w-auto m-0 btn btn-sm bg-white rounded-circle position-absolute end-0 mt-1 mb-1 ml-2 mr-2 z-index-2 d-none d-inline-block d-lg-none" type="button" data-toggle="collapse" data-target="#navbarMainNav" aria-controls="navbarMainNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fi fi-bars"></i>
        </button>
        <!-- /TOP NAVIGATION TOGGLER -->

    </div>
    <!-- /NAVBAR -->

</header>
<!-- /HEADER -->
