jQuery.noConflict();
(function ($) {
  $(function () {
    $(document).ready(function () {
      $("#digStoreConfigTable").DataTable({
        language: {
          decimal: "",
          emptyTable: "No hay informacion para desplegar en la tabla",
          info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
          infoEmpty: "Mostrando 0 de 0 of 0 registros",
          infoFiltered: "(filtrado de _MAX_ registros)",
          infoPostFix: "",
          thousands: ",",
          lengthMenu: "Mostrando _MENU_ registros",
          loadingRecords: "Cargando...",
          processing: "Procesando...",
          search: "Buscar:",
          zeroRecords: "No se encontaron registros con esa busqueda",
          paginate: {
            first: "Primero",
            last: "Ultimo",
            next: "Siguiente",
            previous: "Previo",
          },
          aria: {
            sortAscending: ": activate to sort column ascending",
            sortDescending: ": activate to sort column descending",
          },
        },
      });
    });
  });
})(jQuery);
