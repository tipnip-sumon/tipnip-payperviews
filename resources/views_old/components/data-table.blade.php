<div class="card">
    <div class="card-body table-responsive">
        <table id="{{$table_id}}" class="table  table-vcenter text-nowrap table-bordered border-bottom">
            <thead>
                <tr>
                    @foreach($thead as $th)
                        <th>{{$th}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr>
                    @foreach($thead as $th)
                        <th>{{$th}}</th>
                    @endforeach
                </tr>
            </tfoot>
        </table>
    </div> <!-- /.card-body -->
</div> <!-- /.card -->