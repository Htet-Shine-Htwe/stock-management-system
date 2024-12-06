import DataTable from "datatables.net";

window.addEventListener("DOMContentLoaded", function () {
    // Initialize DataTable

    const refer_product_id = null;
    const table = new DataTable("#stockTable", {
        serverSide: true,
        ajax: "/admin/stocks/load",
        orderMulti: false,
        order: [[4, "desc"]],
        columns: [
            { data: "id" },
            { data: "product" },
            { data: "movementType" },
            { data: "quantity" },
            { data: "createdAt" },
        ],
        drawCallback: function (settings) {
            const api = this.api();
            const data = api.rows({ page: "current" }).data();
            const totalStock = data.reduce((acc, row) => {
                return acc + row.quantity;
            }, 0);

            document.querySelector("#totalStock").textContent = totalStock;
        },
    });
});
b

