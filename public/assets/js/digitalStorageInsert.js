jQuery.noConflict();
(function ($) {
  $(function () {
    $(document).ready(function () {
      var searchFields = { moduleType: $("#moduleType").val() };
      var random = Math.floor(Math.random() * 10000000) + 1;

      $("#selectOV")
        .click(function () {
          $("#digStoreTable_wrapper , #digStoreTable").toggle();
        })
        .attr("disabled", "disabled");
      $.get(
        $("#baseURLAlmacen").val() + "/find?random=" + random,
        searchFields,
        function (data) {
          //digStoreList
          // $("#digStoreListDiv").show();
          var digStoreList = $("#digStoreListDivResult");
          digStoreList.empty();
          var emptyTD = "<td></td>";
          // baseUrl = $("#baseURL").val();
          editable = $("#editable").val();
          $.each(data.digStoreList, function (index, row) {
            if (row.CAPT_POR == -1) {
              // baseUrl = "http://192.168.0.173/muliix-iteknia/public/archivosOV";
            }
            var resultTD =
              "<tr>" +
              // "<td>" +
              // row.LLAVE_ID +
              // "</td>" +
              "<td>" +
              row.GRUPO_ID +
              "</td>" +
              "<td>" +
              row.DOC_ID +
              "</td>";
            if (row.ARCHIVO_1 != "" && row.ARCHIVO_1 != null) {
              resultTD +=
                "<td><a href='" +
                row.ARCHIVO_1 +
                '\' target="blank">Ver Documento</a></td>';
            } else {
              resultTD += emptyTD;
            }
            if (row.ARCHIVO_2 != "" && row.ARCHIVO_2 != null) {
              resultTD +=
                "<td><a href='" +
                row.ARCHIVO_2 +
                '\' target="blank">Ver Documento</a></td>';
            } else {
              resultTD += emptyTD;
            }
            if (row.ARCHIVO_3 != "" && row.ARCHIVO_3 != null) {
              resultTD +=
                "<td><a href='" +
                row.ARCHIVO_3 +
                '\' target="blank">Ver Documento</a></td>';
            } else {
              resultTD += emptyTD;
            }
            if (row.ARCHIVO_4 != "" && row.ARCHIVO_4 != null) {
              resultTD +=
                "<td><a href='" +
                row.ARCHIVO_4 +
                '\' target="blank">Ver Documento</a></td>';
            } else {
              resultTD += emptyTD;
            }
            if (row.ARCHIVO_XML != "" && row.ARCHIVO_XML != null) {
              resultTD +=
                "<td><a href='" +
                row.ARCHIVO_XML +
                '\' target="blank">Ver Documento</a></td>';
            } else {
              resultTD += emptyTD;
            }

            resultTD +=
              "<td><button class='btn btn-info' onclick='" +
              'hideData("GRUPO_ID" , "' +
              row.GRUPO_ID +
              '");' +
              "'>Seleccionar Grupo</button></td>";
            resultTD += "</tr>";
            digStoreList.append(resultTD);
          });
          $("#selectOV").removeAttr("disabled");
        }
      ).always(function () {
        $("#digStoreTable").DataTable({
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
        $("#digStoreTable_wrapper").hide();
      });
    });
  });
})(jQuery);

function hideData(id, value) {
  document.getElementById(id).value = value;
  document.getElementById("digStoreTable_wrapper").style.display = "none";
  document.getElementById("digStoreTable").style.display = "none";
}
