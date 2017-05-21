@extends('layouts.app')

@section('headercss')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <h4>Dashboard</h4>
            <hr />
            @permission('assign-batch-name')
            <div class="col-md-6 well">
                <h5>Assign Batch Name</h5>
                <hr />
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Status</th>
                            <th>Uploaded By</th>
                            <th>Uploaded At</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($batches))
                             @foreach($batches as $batch)
                                @php 
                                    $style = '';
                                    if ($batch->name == 'Temp'.$batch->id)
                                    {
                                @endphp
                                    <tr @php echo $style; @endphp>
                                        <td>{{ $batch->id }}</td>
                                        <td>{{ $batch->status }}</td>
                                        <td>{{ $batch->user->name }}</td>
                                        <td>{{ $batch->created_at }}</td>
                                        <td>{{ count($batch->articles)}}</td>
                                        <td><a class="btn btn-primary btn-xs" href="/batch/show/{{ $batch->id }}/batch" role="button"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Assign Name </a></td>
                                    </tr>
                                @php
                                }
                                @endphp
                            @endforeach
                        @else
                            Nothing to show.
                        @endif
                    </tbody>
                </table>
            </div>
            @endpermission
            @permission('notify-users-batch-available')
            <div class="col-md-5 col-md-offset-1 well">
                <h5>Notify Users Of Job Available</h5>
                <hr />
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Status</th>
                            <th>Uploaded By</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($batches))
                             @foreach($batches as $batch)
                                @if ($batch->status == 'FullyAssigned') 
                                @php
                                    $totalArticles = count($batch->articles);
                                    $unAssigned = count($batch->articles->where('status', 'UnAssigned'));
                                    $assigned = count($batch->articles->where('status', 'Assigned'));
                                    
                                    if ($totalArticles == $assigned) 
                                    {
                                @endphp
                                    <tr @php echo $style; @endphp>
                                        <td>{{ $batch->id }}</td>
                                        <td>{{ $batch->status }}</td>
                                        <td>{{ $batch->created_at }}</td>
                                        <td>{{ count($batch->articles)}}</td>
                                        <td><a class="btn btn-primary btn-xs" href="/batch/{{ $batch->id }}/notify" role="button"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Notify </a></td>
                                    </tr>
                                @php
                                    }
                                @endphp
                                @endif
                            @endforeach
                        @else
                            Nothing to show.
                        @endif
                    </tbody>
                </table>
            </div>
            @endpermission
        </div>
        <div class="row">
            <h4>Available Batches</h4>
            <hr />
            <table class="table table-condensed table-striped" id="BatchTable">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Uploaded By</th>
                    <th>Articles</th>
                    <th>Un-Assigned</th>
                    <th>Assigned</th>
                    <th>Saved</th>
                    <th>Completed</th>
                    <th>Quality Checked</th>
                    <th>Review</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($batches as $batch)
                    @php 
                        $style = '';
                        if ($batch->name == 'Temp'.$batch->id)
                        {
                            continue; 
                            $style = "class='danger'";
                        }
                        
                        $totalArticles = count($batch->articles);
                        $unAssigned = count($batch->articles->where('status', 'UnAssigned'));
                        $assigned = count($batch->articles->where('status', 'Assigned'));
                        
                        $saved = count($batch->articles->where('status', 'Saved'));
                        $completed = count($batch->articles->where('status', 'Completed'));
                        $review = count($batch->articles->where('status', 'Review'));
                        $qcChecked = count($batch->articles->where('status', 'QualityChecked'));
                        
                        $unAssigned = $totalArticles - ($assigned + $saved + $completed + $qcChecked + $review);
                        $assigned = $assigned + $saved + $completed  + $qcChecked + $review;
                        
                        $markAsS = '';
                        if ($batch->status == 'Submitted' || $batch->status == 'QCInProcess')
                        {
                            if ($saved > 0)
                            {
                                $markAsS = '<span class="glyphicon glyphicon-asterisk red" aria-hidden="true"></span>';
                            }
                        }
                        
                    @endphp
                <tr @php echo $style; @endphp>
                    <td>{{ $batch->id }}</td>
                    <td>{{ $batch->name }}</td>
                    <td>{{ $batch->status }}</td>
                    <td>{{ $batch->user->name }}</td>
                    <td>{{ count($batch->articles)}}</td>
                    <td>{{ $unAssigned }}</td>
                    <td>{{ $assigned }}</td>
                    <td>{{ $saved }} {!! $markAsS !!}</td>
                    <td>{{ $completed }}</td>
                    <td>{{ $qcChecked }}</td>
                    <td>{{ $review }}</td>
                    <td>
                        @permission('assign-users-to-batch')
                            @if (($assigned != $totalArticles) && ($batch->status == 'Created' || $batch->status = 'PartiallyAssigned'))
                                <a class="btn btn-sm btn-primary" href="/batch/assign/{{ $batch->id }}" role="button">Assign</a>
                            @endif
                        @endpermission
                        
                        @if ($totalArticles == $completed && $batch->status != 'Submitted')
                        <a class="btn btn-sm btn-success" href="/batch/status/{{ $batch->id }}/submit" role="button">Submit</a>
                        @endif
                        
                        @if ($totalArticles == $completed && $batch->status == 'Submitted')
                            <a class="btn btn-sm btn-success" href="#" role="button" disabled="disabled">Batch Submitted for QC</a>
                        @endif
                        
                       
                            @permission('download-batch')
                                <a class="btn btn-sm btn-success" href="batch/download/{{ $batch->id }}" role="button">Download</a>
                            @endpermission
                       
                        
                        @permission('view-batch')
                        <a class="btn btn-sm btn-info" href="/batch/view/{{ $batch->id }}/batch" role="button">View</a>
                        @endpermission
                        <!--<a class="btn btn-sm btn-info" href="/batch/view/{{ $batch->id }}/batch" role="button">View By User</a>-->
                        @permission('edit-batch-name')
                        <a class="btn btn-sm btn-info" href="/batch/edit/{{ $batch->id }}" role="button">Change Name</a>
                        @endpermission
                        @permission('delete-batch')
                        <a class="btn btn-sm btn-danger" href="/batch/delete/{{ $batch->id }}" role="button">Delete</a>
                        @endpermission
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('footerjs')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    
    <script type="text/javascript">
        $('#BatchTable').DataTable({
            "order": [[ 1, "desc" ]]
        } );
    </script>
@endsection