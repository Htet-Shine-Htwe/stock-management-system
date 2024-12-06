import DataTable from "datatables.net";
import { get, post, del } from "./ajax";

window.addEventListener("DOMContentLoaded", function () {
    // Initialize DataTable

    const product_id = null;
    const table = new DataTable("#productsTable", {
        serverSide: true,
        ajax: "/admin/products/load",
        orderMulti: false,
        order: [[4, "desc"]],
        columns: [
            { data: "name" },
            { data: "price" },
            { data: "stockQuantity" },
            { data: "category" },
            { data: "createdAt" },
            {
                sortable: false,
                data: (row) => `
                    <div class="d-flex">
                        <a href="/admin/products/edit/${row.id}" class="btn btn-outline-warning ms-4">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        <button type="button" class="btn btn-outline-danger ms-2 delete-product-btn" data-id="${row.id}">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                          <button type="button" class="btn btn-outline-success ms-2 add-stock" data-id="${row.id}">
                            <i class="bi bi-plus "></i> 
                        </button>
                    </div>
                `,
            },
        ],
    });

    // Handle delete product action
    document.querySelector("#productsTable").addEventListener("click", function (event) {
        const deleteBtn = event.target.closest(".delete-product-btn");

        if (deleteBtn) {
            const productId = deleteBtn.getAttribute("data-id");

            

            if (confirm("Are you sure you want to delete this product?")) {
                // Show loading indicator
                const loadingIndicator = document.createElement('div');
                loadingIndicator.textContent = "Deleting...";
                loadingIndicator.classList.add('loading-indicator');
                document.body.appendChild(loadingIndicator);

                // Perform the delete operation
                del(`products/delete/${productId}`).then((response) => {
                    loadingIndicator.remove(); // Remove loading indicator

                    if (response.ok) {
                        // Notify success
                        showNotification('Product successfully deleted!', 'success');
                        table.draw(); // Redraw the table
                    } else {
                        // Notify failure
                        showNotification('Failed to delete product. Please try again.', 'error');
                    }
                }).catch((error) => {
                    loadingIndicator.remove(); // Remove loading indicator on error
                    showNotification('An error occurred. Please try again.', 'error');
                });
            }
        }
    });

    document.querySelector("#productsTable").addEventListener("click", function (event) {
        const addStockBtn = event.target.closest(".add-stock");
    
        if (addStockBtn) {
            const productId = addStockBtn.getAttribute("data-id");
    
            // Open the modal and populate the hidden field
            const modal = new bootstrap.Modal(document.getElementById("add-stock-modal"));
            document.getElementById("stock-product-id").value = productId;
            document.getElementById("stock-quantity").value = ""; // Clear previous input
            modal.show();
        }
    });
    
    // Handle the "Add Stock" button in the modal
    document.getElementById("confirm-add-stock").addEventListener("click", function () {
        const productId = document.getElementById("stock-product-id").value;
        const quantity = document.getElementById("stock-quantity").value;
    
        if (!quantity || isNaN(quantity) || Number(quantity) <= 0) {
            alert("Please enter a valid stock quantity.");
            return;
        }
    
        // Show loading indicator
        const loadingIndicator = document.createElement('div');
        loadingIndicator.textContent = "Adding stock...";
        loadingIndicator.classList.add('loading-indicator');
        document.body.appendChild(loadingIndicator);
    
        // Perform the add stock operation
        post(`products/add-stock/${productId}`, { quantity: Number(quantity) })
            .then((response) => {
                loadingIndicator.remove(); // Remove loading indicator
    
                if (response.ok) {
                    // Notify success
                    showNotification('Stock successfully added!', 'success');
                    const modal = bootstrap.Modal.getInstance(document.getElementById("add-stock-modal"));
                    modal.hide(); // Close the modal
                    table.draw(); // Redraw the table
                } else {
                    // Notify failure
                    showNotification('Failed to add stock. Please try again.', 'error');
                }
            })
            .catch((error) => {
                loadingIndicator.remove(); // Remove loading indicator on error
                showNotification('An error occurred. Please try again.', 'error');
            });
    });
    
});


