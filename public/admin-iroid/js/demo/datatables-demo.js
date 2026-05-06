// Call the dataTables jQuery plugin
$(document).ready(function() {
  //$('#dataTable').DataTable({"pageLength": 50, "ordering": false, "ordering": true});
  $('#dataTable').DataTable({"paging": false, "lengthChange": false, "ordering": false, "ordering": false});
});