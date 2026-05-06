function taskComplete(task_id) {
    var id = task_id;
    var url = $('#url').val();
    var formData = {
        id: id,
    };
    $.ajax({
        type: "GET",
        data: formData,
        dataType: 'json',
        url: url + '/' + 'project/project-task/task-complete/' + task_id,
        success: function (data) {
            setTimeout(function () {
                toastr.success('Task Completed !', 'Success Alert', {
                    "iconClass": 'customer-info'
                }, {
                    timeOut: 2000
                });
            }, 500);
            $("#imcomplete_task" + id + "").remove();
            $('.complete_task_list').empty();
            var appendRow = "";
            $.each(data, function (i, value) {
                appendRow += "<li>";
                appendRow += "<input type='checkbox' id='complete_task_single" + value.id + "' class='checkbox' checked value='" + value.id + "' disabled>";
                appendRow += "<label for='complete_task_single" + value.id + "'></label>" + value.title + "-" + value.due_date;
                appendRow += "</li>";
                $('.complete_task_list' + value.assigned_to).html(appendRow);
                console.log(value);
            });
        },
        error: function (data) {
            setTimeout(function () {
                toastr.error('Operation Not Done!', 'Error Alert', {
                    timeOut: 5000
                });
            }, 500);
        }
    });
}