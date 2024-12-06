import DataTable from "datatables.net";
import { get, post, del } from "./ajax";

window.addEventListener("DOMContentLoaded", function () {
    const table = new DataTable("#productsTable", {
        serverSide: true,
        ajax: "/admin/products/load",
        orderMulti: false,
        columns: [
            { data: "name" },
            { data: "price" },
            { 
                data: "stockQuantity",
            },
            {
              data:"category",
            },
            { data: "createdAt" },
            {
                sortable: false,
                data: (row) => `
                    <div class="d-flex">
                        <a href="/products/${row.id}/edit" class="btn btn-outline-primary ms-2">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        <button type="button" class="btn btn-outline-danger ms-2 delete-product-btn" data-id="${row.id}">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </div>
                `,
            },
        ],
    });

    document.querySelector("#productsTable").addEventListener("click", function (event) {
        const deleteBtn = event.target.closest(".delete-product-btn");

        if (deleteBtn) {
            const productId = deleteBtn.getAttribute("data-id");

            if (confirm("Are you sure you want to delete this product?")) {
                del(`products/delete/${productId}`).then((response) => {
                    if (response.ok) {
                        table.draw();
                    }
                });
            }
        }
    });
});
