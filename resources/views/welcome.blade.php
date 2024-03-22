<!DOCTYPE html>
<html>
<head>
    <title>Tasks Table</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <style>
        .completed-tasks {
            background-color: #d4edda; /* Light green background for completed tasks */
        }
        .pending-tasks {
            background-color: #f8d7da; /* Light red background for pending tasks */
        }
    </style>
</head>
<body>

<h2>Pending Tasks</h2>
<div class="w3-responsive">
    <table class="w3-table w3-striped w3-bordered w3-border w3-hoverable w3-white pending-tasks" id="pendingTasksTable">
        <thead>
            <tr class="w3-light-grey">
                <th>UUID</th>
                <th>Description</th>
                <th>Task Type</th>
                <th>Status</th>
                <th>Result</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tasks as $task)
                @if ($task->status !== 'completed')
                <tr>
                    <td>{{ $task->uuid }}</td>
                    <td>{{ $task->description }}</td>
                    <td>{{ $task->task_type }}</td>
                    <td>{{ $task->status }}</td>
                    <td>{{ is_array($task->result) ? json_encode($task->result) : $task->result }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>

<h2>Completed Tasks</h2>
<div class="w3-responsive">
    <table class="w3-table w3-striped w3-bordered w3-border w3-hoverable w3-white completed-tasks" id="completedTasksTable">
        <thead>
            <tr class="w3-light-grey">
                <th>UUID</th>
                <th>Description</th>
                <th>Task Type</th>
                <th>Status</th>
                <th>Result</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tasks as $task)
                @if ($task->status === 'completed')
                <tr>
                    <td>{{ $task->uuid }}</td>
                    <td>{{ $task->description }}</td>
                    <td>{{ $task->task_type }}</td>
                    <td>{{ $task->status }}</td>
                    <td>{{ is_array($task->result) ? json_encode($task->result) : $task->result }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>

<script>
$(document).ready( function () {
    $('#pendingTasksTable').DataTable();
    $('#completedTasksTable').DataTable();
});
</script>

</body>
</html>
