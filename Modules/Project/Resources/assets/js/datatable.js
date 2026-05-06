$(document).ready(function() {
    miDataTable();
    } );
    
    
    
    
    function  miDataTable(){
        $('#miTabla').DataTable({
          "language": {
          "emptyTable":			"<i>No hay datos disponibles en la tabla.</i>",
          "info":		   		"Showing <span class='rows_number'> _START_ </span>",
          "infoEmpty":			"Mostrando 0 registros de un total de 0.",
          "infoFiltered":			"(filtrados de un total de _MAX_ registros)",
          "infoPostFix":			"rows",
          "lengthMenu":			"Mostrar _MENU_ registros",
          "loadingRecords":		"Cargando.",
          "processing":			"Procesando.",
          "search":			"<span style='font-size:15px;'>Buscar:</span>",
          "searchPlaceholder":		"Dato para buscar",
          "zeroRecords":			"No se han encontrado coincidencias.",
          "paginate": {
            "first":			"Primera",
            "last":				"Última",
            "next":				"<span class='pagination-default'>❯</span>",
            "previous":		"<span class='pagination-default'>❮</span>"
          },
          
          "aria": {
            "sortAscending":	"Ordenación ascendente",
            "sortDescending":	"Ordenación descendente"
          }
        },
    
        "lengthMenu":		[[3,5,7, 10, 20, 25, 50, -1], [3,5,7, 10, 20, 25, 50, "Todos"]],
        "iDisplayLength":	4,
    
    
    
    
    
        });
    }
    