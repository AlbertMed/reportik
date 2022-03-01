jQuery.noConflict();
(function ($) {
  $(function () {
    $(document).ready(function () {
      // $("#ventasListDiv, #digStoreListDiv").hide();
      var searchFields = {
        moduleType: $("#moduleType").val(),
        GROUP_ID: $("#GROUP_ID").val(),
        DOC_ID: $("#DOC_ID").val(),
      };
      var pagination = "50vh";
      var random = Math.floor(Math.random() * 10000000) + 1;
      var findURL =
        $("#baseURL").val() +
        "/digitalStorage/ALMACENDIGITAL.json" +
        "?random=" +
        random;
      if ($("#GROUP_ID").val() != "") {
        findURL = $("#baseURLAlmacen").val() + "/find?random=" + random;
        pagination = "";
      }
      var current_url = $(location).attr("href");

      loadData(findURL, searchFields, current_url, false, pagination);
    });

    function loadData(
      findURL,
      searchFields,
      current_url,
      resetSearch = false,
      pagination
    ) {
      $("#digStoreListDiv2").hide();
      $.getJSON(findURL, searchFields, function (data) {
        console.log($("#moduleType").val());
        //digStoreList
        $("#digStoreListDiv").show();
        var digStoreList = $("#digStoreListDivResult");
        digStoreList.empty();
        var emptyTD = "<td></td>";
        // baseUrl = $("#baseURL").val();
        editable = $("#editable").val();
        $.each(data.digStoreList, function (index, row) {
          var url = new URL(current_url);
          if (row.CAPT_POR == -1) {
            // baseUrl = "http://192.168.0.173/muliix-iteknia/public/archivosOV";
          }
          url.searchParams.append("GROUP_ID", row.GRUPO_ID);
          var resultTD =
            "<tr>" +
            // "<td>" +
            // row.LLAVE_ID +
            // "</td>" +
            "<td><a href=" +
            url.toString() +
            ">" +
            row.GRUPO_ID +
            "</a></td>" +
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
            editable == "1"
              ? "<td><a href='" + row.EDIT_URL + "'>Editar</a></td>"
              : "";
          resultTD += "</tr>";

          if (~row.LLAVE_ID.indexOf($("#moduleType").val())) {
            digStoreList.append(resultTD);
          }
        });

        var datatable = $("#digStoreTable1").DataTable({
          destroy: true,
          pageLength: 100,
          // buttons: ["searchBuilder"],
          // dom: "Bfrtip",
          scrollY: pagination,
          autoFill: true,
          // scrollX: true,
          scrollCollapse: true,
          fixedHeader: {
            header: true,
            footer: true,
          },
          language: {
            url: "//cdn.datatables.net/plug-ins/1.11.3/i18n/es-mx.json",
          },
        });
        if (resetSearch) {
          $("#digStoreListDiv1").hide();
          $("#digStoreListDiv2").show();
          $("#digStoreTable2").DataTable({
            destroy: true,
            pageLength: 100,
            // buttons: ["searchBuilder"],
            // dom: "Bfrtip",
            scrollY: 300,
            autoFill: true,
            // scrollX: true,
            scrollCollapse: true,
            // paging: false,
            fixedHeader: {
              header: true,
              footer: true,
            },
            language: {
              url: "//cdn.datatables.net/plug-ins/1.11.3/i18n/es-mx.json",
            },
          });
        }
      });
    }
  });
})(jQuery);
