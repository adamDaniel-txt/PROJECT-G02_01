<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>

    <!-- Same CSS as dashboard -->
    <link rel="stylesheet" href="assets/css/dashStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="app-container">

        <!-- Side Bar-->
        <aside class="sidebar" id="sidebar">
            <div class="logo">
                <div class="logo-icon">D</div>
                <div class="logo-text">Dashboard</div>
            </div>

            <nav class="nav-section">
                <div class="nav-label">Main Menu</div>
                <a href="dashboard.html" class="nav-item">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>

                <a href="analytics.html" class="nav-item">
                    <i class="fa-solid fa-chart-bar"></i>
                    <span>Analytics</span>
                </a>

                <a href="sales.html" class="nav-item">
                    <i class="fa-solid fa-shopping-cart"></i>
                    <span>Sales</span>
                    <span class="nav-badge">12</span>
                </a>

                <a href="products.html" class="nav-item active">
                    <i class="fa-solid fa-box"></i>
                    <span>Product</span>
                </a>

                <a href="customers.html" class="nav-item">
                    <i class="fa-solid fa-users"></i>
                    <span>Customers</span>
                </a>
            </nav>

            <nav class="nav-section">
                <div class="nav-label">Management</div>
                <a href="#" class="nav-item">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span>Orders</span>
                </a>

                <a href="#" class="nav-item">
                    <i class="fa-solid fa-warehouse"></i>
                    <span>Inventory</span>
                </a>

                <a href="#" class="nav-item">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Reports</span>
                </a>
            </nav>

            <nav class="nav-section">
                <div class="nav-label">Support</div>
                <a href="#" class="nav-item">
                    <i class="fa-solid fa-gear"></i>
                    <span>Settings</span>
                </a>

                <a href="#" class="nav-item">
                    <i class="fa-solid fa-circle-question"></i>
                    <span>Help Centre</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="#" class="nav-item">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Log Out</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content">

            <!-- Top Bar -->
            <header class="top-bar">
                <h2>Products</h2>
            </header>

            <!-- Products Content -->
            <div class="dashboard">

                <div class="card">
                    <div class="card-header">
                        Product List
                        <button class="btn">
                            <i class="fa-solid fa-plus"></i> Add Product
                        </button>
                    </div>

                    <table style="width:100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align:left; border-bottom:1px solid #eee;">
                                <th>Product</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>Wireless Headphones</td>
                                <td>Electronics</td>
                                <td>RM 299.00</td>
                                <td>120</td>
                                <td><span style="color: green;">Active</span></td>
                            </tr>

                            <tr>
                                <td>Smart Watch</td>
                                <td>Accessories</td>
                                <td>RM 499.00</td>
                                <td>60</td>
                                <td><span style="color: green;">Active</span></td>
                            </tr>

                            <tr>
                                <td>Laptop Sleeve</td>
                                <td>Office</td>
                                <td>RM 89.00</td>
                                <td>0</td>
                                <td><span style="color: red;">Out of Stock</span></td>
                            </tr>
                        </tbody>
                    </table>

                </div>

            </div>
        </div>
    </div>
</body>

</html>