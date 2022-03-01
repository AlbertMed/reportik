jQuery.noConflict();
(function ($) {
  $(function () {
    $(document).ready(function () {
      var searchFields = { moduleType: $("#moduleType").val() };
      var random = Math.floor(Math.random() * 10000000) + 1;

      $("#selectOV")
        .click(function () {
          $("#ordenTrabajoID").toggle();
        })
        .attr("disabled", "disabled");
      $.get(
        $("#baseURLAlmacen").val() + "/workOrders?random=" + random,
        searchFields,
        function (data) {
          $("#ordenTrabajoID").hide();
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
            var resultTD = "<tr>";
            resultTD += "<td>" + row.OT + "</td>";
            resultTD += "<td>" + row.COD_ARTICULO + "</td>";
            resultTD += "<td>" + row.NOB_ARTICULO + "</td>";
            resultTD += "<td>" + row.OV + "</td>";
            resultTD += "<td>" + row.COD_PROY + "</td>";
            resultTD += "<td>" + row.PROYECTO + "</td>";
            resultTD +=
              "<td><button class='btn btn-info' onclick='" +
              'hideData("GRUPO_ID" , "' +
              row.OT +
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
            url: "//cdn.datatables.net/plug-ins/1.11.3/i18n/es-mx.json",
          },
        });
      });
    });
  });
})(jQuery);

function hideData(id, value) {
  document.getElementById(id).value = value;
  document.getElementById("digStoreTable_wrapper").style.display = "none";
  document.getElementById("digStoreTable").style.display = "none";
}
