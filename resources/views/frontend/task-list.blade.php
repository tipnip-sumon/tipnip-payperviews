<x-smart_layout>
    @section('top_title',$pageTitle)
    @section('title','Task List')
    @section('content')
        @component('components.data-table',['thead'=>
            ['S No.','Purchase Date','Purchase Quantity','Completed','Price','Work Bonus','Expire In']
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
                    url: "{{ route('invest.history') }}"
                    },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'amount', name: 'amount'},
                    {data: 'time_name', name: 'completed'},
                    {data: 'amounts', name: 'amounts'},
                    {data: 'work_bonus', name: 'work_bonus'},
                    {data: 'expire_in', name: 'expire_in'},
                ]
            });
        </script>                   
    @stop
</x-smart_layout>