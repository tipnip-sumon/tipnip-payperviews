<x-smart_layout>
    @section('top_title',$pageTitle)
    @section('title',$pageTitle)
    @section('content')
        @component('components.data-table',['thead'=>
            ['S No.','Date & Time','Trx Type','Amount','Trx ID','Details']
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
                    url: "{{route('user.refferral-history')}}"
                    },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'trx_type', name: 'trx_type'},
                    {data: 'amount', name: 'amount'},
                    {data: 'trx', name: 'trx'},
                    {data: 'remark', name: 'remark'}
                ]
            });
        </script>                   
    @stop
</x-smart_layout>