<x-layout>
    @section('top_title',$pageTitle)
    @section('title',$pageTitle)
    @section('content')
        @component('components.data-table',['thead'=>
            ['S No.','Payment Date','User Name','Order ID','Pay Currency','Pay Wallet','Request Amount','Total Amount','Status']
        ])
            @slot('table_id') city_list @endslot
        @endcomponent
    @endsection
    
    @section('pageJsScripts')
        <!-- DataTables -->
        <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
        <script src="{{asset('assets/js/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('assets/js/dataTables.bootstrap4.min.js')}}"></script>
        <script src="{{asset('assets/js/dataTables.responsive.min.js')}}"></script>
        <script src="{{asset('assets/js/responsive.bootstrap4.min.js')}}"></script>
        <script type="text/javascript">
            
            var table = $("#city_list").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{route('admin.payment')}}"
                    },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'username', name: 'username'},
                    {data: 'payment_id', name: 'payment_id'},
                    {data: 'method_currency', name: 'method_currency'},
                    {data: 'btc_wallet', name: 'btc_wallet'},
                    {data: 'amount', name: 'amount'},
                    {data: 'total_amount', name: 'total_amount'},
                    {data: 'status', name: 'status'}
                ]
            });
        </script>                   
    @stop
</x-layout>