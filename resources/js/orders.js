import DataTable from "datatables.net";

window.addEventListener("DOMContentLoaded", function () {
     new DataTable("#orderTable", {
        serverSide: true,
        ajax: "/admin/orders/load",
        orderMulti: false,
        order: [[0, "desc"]],
        columns: [
            { data: "id" },
            { data: "user_name" },
            { data: "item_count" },
            { data: "total_amount" },
            { data: "status" },
            { data: "order_date" },
        ],
        drawCallback: function (settings) {
            const api = this.api();
            const data = api.rows({ page: "current" }).data();
            const totalStock = data.reduce((acc, row) => {
                return acc + row.total_amount;
            }, 0);

            document.querySelector("#totalAmount").textContent = totalStock;
        },
    });
});
b

