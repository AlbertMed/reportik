jQuery.noConflict();
(function ($) {
  $(function () {
    $(document).ready(function () {
      $("#ventasListDiv, #digStoreListDiv").hide();
    });

    $("#DigStorSalesForm").submit(function (e) {
      e.preventDefault();
      var searchFields = $(this).serialize();
      var random = Math.floor(Math.random() * 10000000) + 1;

      $.get(
        $("#baseURLAlmacen").val() + "/find?random=" + random,
        searchFields,
        function (data) {
          //ventasList
          // $("#ventasListDiv").show();
          // var ventasDiv = $("#ventasListDivResult");
          // ventasDiv.empty();
          // $.each(data.ventasList, function(index,row){
          //     ventasDiv.append("<tr><td>"
          //     + row.OV_CodigoOV + "</td><td>"
          //     + row["CLI_CodigoCliente "] + " " + row.CLI_RazonSocial + "</td><td>"
          //     + row.CMM_Valor + "</td></tr>");
          // });

          //digStoreList
          $("#digStoreListDiv").show();
          var digStoreList = $("#digStoreListDivResult");
          digStoreList.empty();
          var emptyTD = "<td></td>";
          $.each(data.digStoreList, function (index, row) {
            baseUrl = $("#baseURL").val();
            if (row.CAPT_POR == -1) {
              baseUrl = "http://192.168.0.173/muliix-iteknia/public/archivosOV";
            }
            var resultTD =
              "<tr>" +
              "<td>" +
              row.LLAVE_ID +
              "</td>" +
              "<td>" +
              row.GRUPO_ID +
              "</td>" +
              "<td>" +
              row.DOC_ID +
              "</td>";
            if (row.ARCHIVO_1 != "" && row.ARCHIVO_1 != null) {
              resultTD +=
                "<td><a href='" +
                baseUrl +
                "/" +
                row.ARCHIVO_1 +
                '\' target="blank">Ver Documento</a></td>';
            } else {
              resultTD += emptyTD;
            }
            if (row.ARCHIVO_2 != "" && row.ARCHIVO_2 != null) {
              resultTD +=
                "<td><a href='" +
                baseUrl +
                "/" +
                row.ARCHIVO_2 +
                '\' target="blank">Ver Documento</a></td>';
            } else {
              resultTD += emptyTD;
            }
            if (row.ARCHIVO_3 != "" && row.ARCHIVO_3 != null) {
              resultTD +=
                "<td><a href='" +
                baseUrl +
                "/" +
                row.ARCHIVO_3 +
                '\' target="blank">Ver Documento</a></td>';
            } else {
              resultTD += emptyTD;
            }
            if (row.ARCHIVO_4 != "" && row.ARCHIVO_4 != null) {
              resultTD +=
                "<td><a href='" +
                baseUrl +
                "/" +
                row.ARCHIVO_4 +
                '\' target="blank">Ver Documento</a></td>';
            } else {
              resultTD += emptyTD;
            }
            if (row.ARCHIVO_XML != "" && row.ARCHIVO_XML != null) {
              resultTD +=
                "<td><a href='" +
                baseUrl +
                "/" +
                row.ARCHIVO_XML +
                '\' target="blank">Ver Documento</a></td>';
            } else {
              resultTD += emptyTD;
            }

            resultTD += "<td><a href='" + row.EDIT_URL + "'>Editar</a></td>";
            resultTD += "</tr>";
            digStoreList.append(resultTD);
          });
        }
      );
    });
  });
})(jQuery);
