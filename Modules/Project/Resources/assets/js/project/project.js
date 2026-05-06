//Complete task
function projectComplete(project_id) {
    var id = project_id;
    var url = $('#url').val();
    var formData = {
        id: id,
    };
    console.log(formData);
    $.ajax({
        type: "GET",
        data: formData,
        dataType: 'json',
        url: url + '/' + 'project/project-complete/' + project_id,
        success: function (data) {
            setTimeout(function () {
                toastr.success('Project Completed!', 'Success Alert', {
                    "iconClass": 'customer-info'
                }, {
                    timeOut: 2000
                });
            }, 500);
            console.log(data);

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
//Incomplete project
function projectIncomplete(project_id) {
    var id = project_id;
    var url = $('#url').val();
    var formData = {
        id: id,
    };
    $.ajax({
        type: "GET",
        data: formData,
        dataType: 'json',
        url: url + '/' + 'project/project-incomplete/' + project_id,
        success: function (data) {
            setTimeout(function () {
                toastr.success('Project Incompleted !', 'Success Alert', {
                    "iconClass": 'customer-info'
                }, {
                    timeOut: 2000
                });
            }, 500);
            console.log(data);

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