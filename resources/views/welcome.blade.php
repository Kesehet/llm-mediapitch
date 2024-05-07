<!DOCTYPE html>
<html>
<head>
    <title>Tasks Table</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.6/js/dataTables.min.js"></script>
    <link href="https://cdn.datatables.net/2.0.6/css/dataTables.dataTables.min.css" rel="stylesheet" type="text/css" />
    <style>
        /* body style for dark mode */
        body {
            background-color: #000;
            color: #fff;
        }
        .completed-tasks {
            background-color: #d4edda; /* Light green background for completed tasks */
            
        }
        .pending-tasks {
            background-color: #f8d7da; /* Light red background for pending tasks */
        }
        .processing-tasks {
            background-color: #ddd7f8; /* Light yellow background for processing tasks */
        }
        .closed-tasks {
            background-color: #f8d7d7; /* Light red background for closed tasks */
        }
    </style>
</head>
<body>



<h2> Machines Table</h2>
<div class="w3-responsive">
    <table id="machines" class="w3-table w3-bordered w3-border w3-hoverable w3-white">
        <thead>
            <tr class="w3-light-grey">
                <th>Status</th>
                <th>Name</th>
                <th>Machine ID</th>                
                <th>Price</th>
                <th>Last Active</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($machines as $machine)

                <tr>
                    <td>{{ $machine->status }}</td>    
                    <td>{{ $machine->name }}</td>
                    <td>{{ $machine->machine_id }}</td>
                    <td>{{ $machine->price }}</td>
                    <td>{{ $machine->updated_at->timezone('Asia/Kolkata')->toDateTimeString() }}</td>
                    <td>{{ $machine->created_at->timezone('Asia/Kolkata')->toDateTimeString() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<h2>Pending Tasks</h2>
<div class="w3-responsive">
    <table class="w3-table w3-bordered w3-border w3-hoverable w3-white" id="pendingTasksTable">
        <thead>
            <tr class="w3-light-grey">
                <th>Updated At</th>
                <th>Created At</th>
                <th>UUID</th>
                <th>Description</th>
                <th>Task Type</th>
                <th>Status</th>
                <th>Payload</th>
                <th>Result</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tasks as $task)
                @if ($task->status !== 'completed')
                <tr class="{{$task->status}}-tasks">
                    <td>{{ $task->updated_at->timezone('Asia/Kolkata')->toDateTimeString() }}</td>
                    <td>{{ $task->created_at->timezone('Asia/Kolkata')->toDateTimeString() }}</td>
                    <td>{{ $task->uuid }}</td>
                    <td>{{ $task->description }}</td>
                    <td>{{ $task->task_type }}</td>
                    <td>{{ $task->status }}</td>
                    <td>{{ is_array($task->payload) ? json_encode($task->payload) : $task->payload }}</td>
                    <td>{{ is_array($task->result) ? json_encode($task->result) : $task->result }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>

<h2>Completed Tasks</h2>
<div class="w3-responsive">
    <table class="w3-table w3-bordered w3-border w3-hoverable w3-white" id="completedTasksTable">
        <thead>
            <tr class="w3-light-grey">
                <th> Difference between created and updated </th>
                <th>UUID</th>
                <th>Task Type</th>
                <th>Status</th>
                <th>Payload</th>
                <th>Result</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tasks as $task)
                @if ($task->status === 'completed')
                <tr class="{{$task->status}}-tasks">
                    <td>{{ $task->updated_at->timezone('Asia/Kolkata')->diffForHumans($task->created_at->timezone('Asia/Kolkata')) }}</td>
                    <td>{{ $task->uuid }}</td>
                    <td>{{ $task->task_type }}</td>
                    <td>{{ $task->status }}</td>
                    <td>{{ is_array($task->payload) ? json_encode($task->payload) : $task->payload }}</td>
                    <td>{{ is_array($task->result) ? json_encode($task->result) : $task->result }}</td>
                    <td>{{ $task->created_at->timezone('Asia/Kolkata')->toDateTimeString() }}</td>
                    <td>{{ $task->updated_at->timezone('Asia/Kolkata')->toDateTimeString() }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>



<script>
$(document).ready( function () {
    $('#pendingTasksTable').DataTable(
        {
            "order": [[ 0, "desc" ]]
        }
    );
    $('#completedTasksTable').DataTable(
        {
            "order": [[ 0, "desc" ]]
        }
    );
    $('#machines').DataTable(
        {
            "order": [[ 0, "desc" ]]
        }
    );
});
</script>

</body>
</html>
