<!DOCTYPE html>
<html lang="en">
@include('my_project.backend.layouts.head')
<body>
    <!------------------------------------------------------------------------------------------------>
    @include('my_project.backend.layouts.pre-loader')
    <!-- WRAPPER ------------------------------------------------------------------------------------->
    <div id="app">
        <!-- Wrapper Start -->
        <div class="wrapper">
            <!------------------------------------------------------------------------------------------------>
            @include('my_project.backend.menus.left-menu')
            <!------------------------------------------------------------------------------------------------>
            <!-- Page Content  -->
            <div id="content-page" class="content-page">
                @include('my_project.backend.menus.header-menu')
                <!------------------------------------------------------------------------------------------------>
                <main class="">
                  <div id="main-body" class="container-fluid">
                    @yield('main-content')
                  </div>
                </main>
                <!------------------------------------------------------------------------------------------------>
                @include('my_project.backend.layouts.footer')
            </div>
            <div class="app-loader">
                <i class="fa fa-spinner fa-spin"></i>
            </div>
        </div>
        <!-- END WRAPPER --------------------------------------------------------------------------------->
    </div>
    <!------------------------------------------------------------------------------------------------>
    @include('my_project.backend.layouts.script')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    @yield('page-script')
    @include('my_project.backend.layouts.toster-script')
</body>

</html>
