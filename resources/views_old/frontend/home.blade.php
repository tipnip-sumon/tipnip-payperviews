<x-smart_layout>
    @section('top_title','Dashboard')
    @section('title','Dashboard')
    @section('content')
        <div class="row">
            <div class="col-sm-12 col-md-6 col-xl-3">
                <div class="card custom-card bg-primary">
                    <div class="card-body">
                        <div class="d-flex no-block align-items-center">
                            <div>
                                <h6 class="text-fixed-white">Wallet</h6>
                                <h2 class="text-fixed-white m-0 font-weight-bold">${{ showAmount(auth()->user()->deposit_wallet) }}</h2>
                            </div>
                            <div class="ms-auto">
                                <span class="text-fixed-white display-6"><i class="fa-regular fa-file-lines fa-2x"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-xl-3">
                <div class="card custom-card bg-secondary">
                    <div class="card-body">
                        <div class="d-flex no-block align-items-center">
                            <div>
                                <h6 class="text-fixed-white">Sales</h6>
                                <h2 class="text-fixed-white m-0 font-weight-bold">25k</h2>
                            </div>
                            <div class="ms-auto">
                                <span class="text-fixed-white display-6"><i class="fa fa-signal fa-2x"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-xl-3">
                <div class="card custom-card bg-warning">
                    <div class="card-body">
                        <div class="d-flex no-block align-items-center">
                            <div>
                                <h6 class="text-fixed-white">Profit</h6>
                                <h2 class="text-fixed-white m-0 font-weight-bold">62K</h2>
                            </div>
                            <div class="ms-auto">
                                <span class="text-fixed-white display-6"><i class="fa fa-usd fa-2x"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-xl-3">
                <div class="card custom-card bg-info">
                    <div class="card-body">
                        <div class="d-flex no-block align-items-center">
                            <div>
                                <h6 class="text-fixed-white">News</h6>
                                <h2 class="text-fixed-white m-0 font-weight-bold">542</h2>
                            </div>
                            <div class="ms-auto">
                                <span class="text-fixed-white display-6"><i class="fa-regular fa-newspaper fa-2x"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4 col-sm-4 col-lg-2">
                <div class="card custom-card">
                    <div class="card-body text-center">
                        <div class="h2 m-0 font-weight-bold"><i class="mdi mdi-account-multiple-outline text-primary"></i></div>
                        <div class="text-muted mb-0"> Custom</div>
                    </div>
                </div>
            </div>
            <div class="col-4 col-sm-4 col-lg-2">
                <div class="card custom-card">
                    <div class="card-body text-center">
                        <div class="h2 m-0 font-weight-bold"><i class="mdi mdi-cash-multiple text-red"></i></div>
                        <div class="text-muted mb-0"> Sales</div>
                    </div>
                </div>
            </div>
            <div class="col-4 col-sm-4 col-lg-2">
                <div class="card custom-card">
                    <div class="card-body text-center">
                        <div class="h2 m-0 font-weight-bold"><i class="mdi mdi-chart-line text-warning"></i></div>
                        <div class="text-muted mb-0"> Orders</div>
                    </div>
                </div>
            </div>
            <div class="col-4 col-sm-4 col-lg-2">
                <div class="card custom-card">
                    <div class="card-body text-center">
                        <div class="h2 m-0 font-weight-bold"><i class="mdi mdi-account-outline text-info"></i></div>
                        <div class="text-muted mb-0"> Invoice</div>
                    </div>
                </div>
            </div>
            <div class="col-4 col-sm-4 col-lg-2">
                <div class="card custom-card">
                    <div class="card-body text-center">
                        <div class="h2 m-0 font-weight-bold"><i class="mdi mdi-chart-line text-warning"></i></div>
                        <div class="text-muted mb-0"> Orders</div>
                    </div>
                </div>
            </div>
            <div class="col-4 col-sm-4 col-lg-2">
                <div class="card custom-card">
                    <div class="card-body text-center">
                        <div class="h2 m-0 font-weight-bold"><i class="mdi mdi-account-outline text-info"></i></div>
                        <div class="text-muted mb-0"> Invoice</div>
                    </div>
                </div>
            </div>
            <div class="col-4 col-sm-4 col-lg-2">
                <div class="card custom-card">
                    <div class="card-body text-center">
                        <div class="h2 m-0 font-weight-bold"><i class="mdi mdi-account-outline text-info"></i></div>
                        <div class="text-muted mb-0"> Invoice</div>
                    </div>
                </div>
            </div>
            <div class="col-4 col-sm-4 col-lg-2">
                <div class="card custom-card">
                    <div class="card-body text-center">
                        <div class="h2 m-0 font-weight-bold"><i class="mdi mdi-chart-line text-warning"></i></div>
                        <div class="text-muted mb-0"> Orders</div>
                    </div>
                </div>
            </div>
            <div class="col-4 col-sm-4 col-lg-2">
                <div class="card custom-card">
                    <div class="card-body text-center">
                        <div class="h2 m-0 font-weight-bold"><i class="mdi mdi-account-outline text-info"></i></div>
                        <div class="text-muted mb-0"> Invoice</div>
                    </div>
                </div>
            </div>
            <div class="col-4 col-sm-4 col-lg-2">
                <div class="card custom-card">
                    <div class="card-body text-center">
                        <div class="h2 m-0 font-weight-bold"><i class="mdi mdi-account-outline text-info"></i></div>
                        <div class="text-muted mb-0"> Invoice</div>
                    </div>
                </div>
            </div>
            <div class="col-4 col-sm-4 col-lg-2">
                <div class="card custom-card">
                    <div class="card-body text-center">
                        <div class="h2 m-0 font-weight-bold"><i class="mdi mdi-chart-line text-warning"></i></div>
                        <div class="text-muted mb-0"> Orders</div>
                    </div>
                </div>
            </div>
            <div class="col-4 col-sm-4 col-lg-2">
                <div class="card custom-card">
                    <div class="card-body text-center">
                        <div class="h2 m-0 font-weight-bold"><i class="mdi mdi-account-outline text-info"></i></div>
                        <div class="text-muted mb-0"> Invoice</div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
</x-smart_layout>