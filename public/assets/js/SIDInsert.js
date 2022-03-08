jQuery.noConflict();
(function ($) {
  $(function () {
    $(document).ready(function () {
      $("#OTInputFormDiv, #submitButtonDiv").hide();
      $("#btnSearchOT").click(function (event) {
        event.preventDefault();
        var random = Math.floor(Math.random() * 10000000) + 1;
        var url =
          $("#baseURLAlmacen").val() +
          "/workOrders/" +
          $("#searchOT").val() +
          "/?random=" +
          random;
        $.get(url, function (data) {
          $("#searchOTThead").empty();
          $("#searchOTTbody").empty();
          if ($.isEmptyObject(data) || data == "[]") {
            alert("OT no encontrado -- " + $("#searchOT").val());
          } else {
            var result = jQuery.parseJSON(data);
            $.each(result, function (i, item) {
              if (i == "columns") {
                //COLUMNS
                var columns = "";
                var rows = "";
                $.each(item, function (i, columnName) {
                  columns += "<td scope='col'>" + columnName + "</td>";
                  rows +=
                    "<td scope='col'>" + result["data"][columnName] + "</td>";
                });
                $("#searchOTThead").append(columns);
                $("#searchOTTbody").append(rows);
              }
              var message =
                "Esta Orden de Trabajo (" +
                $("#searchOT").val() +
                ") ya fue cerrada, quiere continuar?";
              if (i == "workOrderClosed" && item == true && confirm(message)) {
                $("#OTInputFormDiv, #submitButtonDiv").show();
                $("#OTInsertOT").val($("#searchOT").val());
                $("#OTInsertArea").val($("#department").val());
              } else {
                $("#OTInputFormDiv, #submitButtonDiv").hide();
              }
            });
          }
        });
      });
    });
  });
})(jQuery);
