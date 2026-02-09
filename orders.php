<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Orders</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="assets/css/dashStyle.css">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">

    <style>
        .order-table {
            width: 100%;
            border-collapse: collapse;
        }

        .order-table th,
        .order-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        .order-table select {
            padding: 6px;
        }

        .status {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            color: white;
        }

        .pending { background: #f0ad4e; }
        .processing { background: #5bc0de; }
        .completed { background: #5cb85c; }
        .cancelled { background: #d9534f; }

        .order-table button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin-right: 8px;
        }
    </style>
</head>

<body>
<div class="app-container">

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="logo">
            <div class="logo-text">Dashboard</div>
        </div>

        <nav class="nav-section">
            <div class="nav-label">Main Menu</div>
            <a href="dashboard.php" class="nav-item">
                <i class="bi bi-graph-up"></i>
                <span>Dashboard</span>
            </a>

            <a href="analytics.php" class="nav-item">
                <i class="bi bi-bar-chart"></i>
                <span>Analytics</span>
            </a>

            <a href="sales.php" class="nav-item">
                <i class="bi bi-cart3"></i>
                <span>Sales</span>
            </a>

            <a href="menu_items.php" class="nav-item">
                <i class="bi bi-box"></i>
                <span>Menu Items</span>
            </a>

            <a href="customers.php" class="nav-item">
                <i class="bi bi-people"></i>
                <span>Customers</span>
            </a>

            <a href="orders.php" class="nav-item active">
                <i class="bi bi-receipt"></i>
                <span>Orders</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="profile.php" class="nav-item">
                <i class="bi bi-person"></i>
                <span>Profile</span>
            </a>
            <a href="index.php" class="nav-item">
                <i class="bi bi-box-arrow-right"></i>
                <span>Go Home</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <div class="col-md-13 col-lg-12 p-4">
            <!-- Messages -->
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <div class="top-bar d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="bi bi-receipt me-2"></i>Orders
                </h1>
            </div>

            <!-- Insert Function -->





        </div>

        <div class="dashboard">
            <div class="card">
                <div class="card-header">Orders List</div>

                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total (RM)</th>
                            <th>Status</th>
                            <th>Update Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="orderTable">
                        <!-- Orders will show up here-->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Actions Modal -->
<div class="modal fade" id="actionsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Order Actions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="actionsModalBody"></div>

        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Connect database
    // Fetch orders from database here

    let orders = []; // Dummy data

    const table = document.getElementById("orderTable");

    function statusClass(status) {
        return status.toLowerCase();
    }

    function renderOrders() {
        table.innerHTML = "";

        // If there are NO orders
        if (orders.length === 0) {
            table.innerHTML = `
                <tr>
                    <td colspan="6" style="text-align:center; padding:20px; color:#777;">
                        No orders found
                    </td>
                </tr>
            `;
            return;
        }

        // If orders EXIST
        orders.forEach((order, index) => {
            table.innerHTML += `
                <tr>
                    <td>${order.id}</td>
                    <td>${order.customer}</td>
                    <td>${order.total.toFixed(2)}</td>
                    <td>
                        <span class="status ${statusClass(order.status)}">
                            ${order.status}
                        </span>
                    </td>
                    <td>
                        <select onchange="updateStatus(${index}, this.value)">
                            <option ${order.status === "Pending" ? "selected" : ""}>Pending</option>
                            <option ${order.status === "Processing" ? "selected" : ""}>Processing</option>
                            <option ${order.status === "Completed" ? "selected" : ""}>Completed</option>
                            <option ${order.status === "Cancelled" ? "selected" : ""}>Cancelled</option>
                        </select>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary"
                            onclick="openActionsModal(${index})">
                            Actions
                        </button>
                    </td>
                </tr>
            `;
        });
    }

    function viewOrder(index) {
        const o = orders[index];
        alert(
            `Order ID: ${o.id}\n` +
            `Customer: ${o.customer}\n` +
            `Total: RM ${o.total}\n` +
            `Status: ${o.status}`
        );
    }

    function updateStatus(index, newStatus) {
        if (orders[index].status === "Cancelled") {
            alert("Cancelled orders cannot be updated.");
            return;
        }

        orders[index].status = newStatus;
        renderOrders();
    }

    function cancelOrder(index) {
        if (confirm("Are you sure you want to cancel this order?")) {
            orders[index].status = "Cancelled";
            renderOrders();
        }
    }

    renderOrders();
</script>

<script>
    let selectedOrderIndex = null;

    function openActionsModal(index) {
        selectedOrderIndex = index;

        const modalBody = document.getElementById("actionsModalBody");
        modalBody.innerHTML = `
            <button class="btn btn-primary w-100 mb-2"
                onclick="showOrderDetails()">
                View Order
            </button>

            <button class="btn btn-danger w-100"
                onclick="confirmCancelOrder()">
                Cancel Order
            </button>
        `;

        new bootstrap.Modal(document.getElementById("actionsModal")).show();
    }

    function showOrderDetails() {
        const o = orders[selectedOrderIndex];

        document.getElementById("actionsModalBody").innerHTML = `
            <p><strong>Order ID:</strong> ${o.id}</p>
            <p><strong>Customer:</strong> ${o.customer}</p>
            <p><strong>Time of Order:</strong> ${o.time || "N/A"}</p>
            <p><strong>Items Ordered:</strong><br>${o.items || "N/A"}</p>
            <p><strong>Remarks:</strong> ${o.remarks || "None"}</p>
            <p><strong>Total:</strong> RM ${o.total.toFixed(2)}</p>

            <button class="btn btn-secondary w-100 mt-3"
                onclick="openActionsModal(selectedOrderIndex)">
                Back
            </button>
        `;
    }

    function confirmCancelOrder() {
        document.getElementById("actionsModalBody").innerHTML = `
            <p>Are you sure you want to cancel this order?</p>

            <div class="d-flex gap-2">
                <button class="btn btn-danger w-50"
                    onclick="showCancellationReason()">
                    Yes
                </button>
                <button class="btn btn-secondary w-50"
                    data-bs-dismiss="modal">
                    No
                </button>
            </div>
        `;
    }

    function showCancellationReason() {
        document.getElementById("actionsModalBody").innerHTML = `
            <label class="form-label">Reason for cancellation</label>
            <textarea class="form-control mb-3"
                id="cancelReason"
                placeholder="e.g. Fake order"></textarea>

            <div class="d-flex gap-2">
                <button class="btn btn-danger w-50"
                    onclick="submitCancellation()">
                    Submit Cancellation
                </button>
                <button class="btn btn-secondary w-50"
                    onclick="openActionsModal(selectedOrderIndex)">
                    Don't cancel
                </button>
            </div>
        `;
    }

    function submitCancellation() {
        const reason = document.getElementById("cancelReason").value.trim();

        if (!reason) {
            alert("Please provide a reason for cancellation.");
            return;
        }

        // ===============================
        // Connect database
        // Save cancellation + reason here
        // ===============================

        orders[selectedOrderIndex].status = "Cancelled";
        orders[selectedOrderIndex].cancelReason = reason;

        bootstrap.Modal.getInstance(
            document.getElementById("actionsModal")
        ).hide();

        renderOrders();
    }
</script>

</body>
</html>
