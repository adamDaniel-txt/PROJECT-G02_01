<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Dashboard</title>
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
                <a href="dashboard.html" class="nav-item active">
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
                    <!-- <span class="nav-badge">12</span> -->
                </a>

                <a href="menu_items.php" class="nav-item">
                    <i class="fa-solid fa-box"></i>
                    <span>Menu Items</span>
                </a>

                <a href="customers.html" class="nav-item">
                    <i class="fa-solid fa-users"></i>
                    <span>Customers</span>
                </a>
            </nav>

            <nav hidden class="nav-section">
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

            <nav hidden class="nav-section">
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

        <!--Main Content-->
        <div class="main-content">
            <!--Top Bar-->
            <header class="top-bar">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div class="search-bar">
                        <i class="fa-solid fa-search"></i>
                        <input type="text" placeholder="Search..."> <!--Dummy-->
                    </div>
                </div>
                <div class="top-actions">
                    <button class="icon-button">
                        <i class="fa-solid fa-bell"></i>
                        <span class="notification-badge">.</span>
                    </button>

                    <!--Sample Profile-->
                    <button class="profile-button">
                        <div class="profile-info">
                            <div class="profile-name">Hazim Faiz</div>
                            <div class="profile-role">Admin</div>
                        </div>
                        <div class="profile-avatar">HZ</div>
                    </button>
                </div>
            </header>

            <!--Dashboard Content-->
            <div class="dashboard">
                <!--Metric Cards-->
                <div class="metric-grid">
                    <div class="metric-card">
                        <div class="metric-header">
                            Active Sales <i class="fa-solid fa-circle-info"></i>
                        </div>
                        <div class="metric-value">RM67,000.00</div> <!-- Dummy value -->
                        <div class="metric-change positive">
                            vs last month <i class="fa-solid fa-arrow-up"></i> 12%
                        </div>
                        <div class="metric-icon">
                            <div class="bar-chart-icon">
                                <div class="bar" style="height: 35px;"></div>
                                <div class="bar" style="height: 45px;"></div>
                                <div class="bar" style="height: 40px;"></div>
                            </div>
                        </div>
                        <a href="#" class="see-details">See Details <i class="fa-solid fa-arrow-right"></i></a>
                    </div>

                    <div class="metric-card">
                        <div class="metric-header">
                            Product Revenue <i class="fa-solid fa-circle-info"></i>
                        </div>
                        <div class="metric-value">RM27,000.00</div> <!-- Dummy value -->
                        <div class="metric-change positive">
                            vs last month <i class="fa-solid fa-arrow-up"></i> 9%
                        </div>
                        <div class="metric-icon">
                            <svg class="line-chart-svg" viewBox="0 0 50 50">
                                <polyline points="5,35 15,25 25,30 35,15 45,20" stroke="#94A1EE" stroke-width="3"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <a href="#" class="see-details">See Details <i class="fa-solid fa-arrow-right"></i></a>
                    </div>

                    <div class="metric-card">
                        <div class="metric-header">
                            Product Sold <i class="fa-solid fa-circle-info"></i>
                        </div>
                        <div class="metric-value">RM13,900.00</div> <!-- Dummy value -->
                        <div class="metric-change positive">
                            vs last month <i class="fa-solid fa-arrow-up"></i> 7%
                        </div>
                        <div class="metric-icon">
                            <div class="gauge-icon">
                                <div class="gauge-bg">
                                    <div class="gauge-fill"></div>
                                </div>
                            </div>
                        </div>
                        <a href="#" class="see-details">See Details <i class="fa-solid fa-arrow-right"></i></a>
                    </div>

                    <div class="metric-card">
                        <div class="metric-header">
                            Conversion Rate <i class="fa-solid fa-circle-info"></i>
                        </div>
                        <div class="metric-value">10%</div> <!-- Dummy value -->
                        <div class="metric-change negative">
                            vs last month <i class="fa-solid fa-arrow-up"></i> 2%
                        </div>
                        <div class="metric-icon">
                            <div class="mini-bars">
                                <div class="mini-bar" style="height: 35px;"></div>
                                <div class="mini-bar" style="height: 40px;"></div>
                                <div class="mini-bar" style="height: 25px;"></div>
                                <div class="mini-bar" style="height: 45px;"></div>
                                <div class="mini-bar" style="height: 20px;"></div>
                            </div>
                        </div>
                        <a href="#" class="see-details">See Details <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>

                <!-- Main Content Grid-->
                <div class="content-grid">
                    <div class="card">
                        <div class="card-header">
                            Sales Performance <i class="fa-solid fa-circle-info"></i>
                        </div>

                        <svg class="gauge-arc" viewBox="0 0 200 120">
                            <defs>
                                <linearGradient id="gaugeGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" style="stop-color:#DCE2FC" />
                                    <stop offset="50%" style="stop-color:#AAB7F8" />
                                    <stop offset="100%" style="stop-color:#94A1EE" />
                                </linearGradient>
                            </defs>
                            <path d="M 20 100 A 80 80 0 0 1 180 100" stroke="#f0f0f0" stroke-width="20" fill="none"
                                stroke-linecap="round" />
                            <path d="M 20 100 A 80 80 0 0 1 148 100" stroke="url(#gaugeGradient)" stroke-width="20"
                                fill="none" stroke-linecap="round" />
                        </svg>

                        <div>
                            <span class="score-value">82</span>
                            <span class="score-badge">+1</span>
                            <span class="score-label">of 100 points</span>
                        </div>
                        <div class="team-message">
                            <h3>You're team is great!</h3>
                            <p>The team is performing well above average, meeting or exceeding targets in several areas.
                            </p>
                        </div>
                        <a href="#" class="btn-link">Improve Your Score <i class="fa-solid fa-arrow-right"></i></a>
                    </div>

                    <!-- Analytics Card -->
                    <div class="card">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div class="card-header">
                                Analytics <i class="fa-solid fa-circle-info"></i>
                            </div>

                            <div class="chart-control">
                                <button class="btn"><i class="fa-solid fa-filter"></i>Filter</button>
                                <button class="btn">Last Year<i class="fa-solid fa-chevron-down"></i></button>
                                <button class="btn"><i class="fa-solid fa-expand"></i></button>
                            </div>
                        </div>

                        <div class="analytics-chart">
                            <div class="chart-bars">
                                <div class="chart-bar" style="height: 15%;">
                                    <span class="chart-label">Jan</span>
                                </div>

                                <div class="chart-bar" style="height: 20%;">
                                    <span class="chart-label">Feb</span>
                                </div>

                                <div class="chart-bar" style="height: 25%;">
                                    <span class="chart-label">Mar</span>
                                </div>

                                <div class="chart-bar" style="height: 18%;">
                                    <span class="chart-label">Apr</span>
                                </div>

                                <div class="chart-bar" style="height: 22%;">
                                    <span class="chart-label">May</span>
                                </div>

                                <div class="chart-bar active" style="height: 85%;">
                                    <div class="chart-tooltip">
                                        <div>Jun: 2025</div>
                                        <div>Revenue: RM16,500.00</div>
                                        <div>Conversion Rate: 6.7%</div>
                                    </div>
                                    <span class="chart-label">Jun</span>
                                </div>

                                <div class="chart-bar" style="height: 12%;">
                                    <span class="chart-label">Jul</span>
                                </div>

                                <div class="chart-bar" style="height: 16%;">
                                    <span class="chart-label">Aug</span>
                                </div>

                                <div class="chart-bar" style="height: 19%;">
                                    <span class="chart-label">Sep</span>
                                </div>

                                <div class="chart-bar" style="height: 14%;">
                                    <span class="chart-label">Oct</span>
                                </div>

                                <div class="chart-bar" style="height: 17%;">
                                    <span class="chart-label">Nov</span>
                                </div>

                                <div class="chart-bar" style="height: 21%;">
                                    <span class="chart-label">Dec</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bottom-grid">
                        <div class="card">
                            <div class="card-header">
                                Visit by Time <i class="fa-solid fa-circle-info"></i>
                            </div>

                            <div class="heatmap">
                                <div class="heatmap-grid">
                                    <div></div>
                                    <div style="text-align: center; color: #999; font-size: 12px;">Mon</div>
                                    <div style="text-align: center; color: #999; font-size: 12px;">Tue</div>
                                    <div style="text-align: center; color: #999; font-size: 12px;">Wed</div>
                                    <div style="text-align: center; color: #999; font-size: 12px;">Thu</div>
                                    <div style="text-align: center; color: #999; font-size: 12px;">Fri</div>
                                    <div style="text-align: center; color: #999; font-size: 12px;">Sat</div>
                                    <div style="text-align: center; color: #999; font-size: 12px;">Sun</div>

                                    <div class="heatmap-label">12AM - 8AM</div>
                                    <div class="heatmap-cell medium"></div>
                                    <div class="heatmap-cell empty"></div>
                                    <div class="heatmap-cell low"></div>
                                    <div class="heatmap-cell low"></div>
                                    <div class="heatmap-cell empty"></div>
                                    <div class="heatmap-cell low"></div>
                                    <div class="heatmap-cell empty"></div>

                                    <div class="heatmap-label">8AM - 4PM</div>
                                    <div class="heatmap-cell high"></div>
                                    <div class="heatmap-cell low"></div>
                                    <div class="heatmap-cell high"></div>
                                    <div class="heatmap-cell medium"></div>
                                    <div class="heatmap-cell low"></div>
                                    <div class="heatmap-cell medium"></div>
                                    <div class="heatmap-cell high"></div>

                                    <div class="heatmap-label">4PM - 12AM</div>
                                    <div class="heatmap-cell low"></div>
                                    <div class="heatmap-cell medium"></div>
                                    <div class="heatmap-cell empty"></div>
                                    <div class="heatmap-cell empty"></div>
                                    <div class="heatmap-cell very-high"></div>
                                    <div class="heatmap-cell empty"></div>
                                    <div class="heatmap-cell medium"></div>
                                </div>

                                <div class="heatmap-legend">
                                    <span>0</span>
                                    <div class="legend-scale">
                                        <div class="legend-box" style="background: #C2C9FA;"></div>
                                        <div class="legend-box" style="background: #AAB7F8;"></div>
                                        <div class="legend-box" style="background: #7183F2;"></div>
                                        <div class="legend-box" style="background: #5A70F0;"></div>
                                    </div>
                                    <span>10,000+</span>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                Total Visit <i class="fa-solid fa-circle-info"></i>
                            </div>

                            <div class="visits-content">
                                <div class="total-visits">
                                    <div class="total-number">191,886</div>
                                    <div class="total-change">
                                        vs last month <i class="fa-solid fa-arrow-up"></i>8.5%
                                    </div>
                                </div>

                                <div style="display: flex; align-items: center; gap: 40px;">
                                    <div class="visit-stats" style="flex: 1;">
                                        <div class="stat-item">
                                            <div class="stat-label">
                                                <span class="stat-dot mobile"></span>
                                                Mobile
                                            </div>
                                            <div class="stat-value">115,132</div> <!-- Dummy Data-->
                                        </div>

                                        <div class="stat-item">
                                            <div class="stat-label">
                                                <span class="stat-dot website"></span>
                                                website
                                            </div>
                                            <div class="stat-value">76,754</div> <!-- Dummy Data-->
                                        </div>
                                    </div>

                                    <div class="pie-chart">
                                        <svg viewBox="0 0 200 200">
                                            <circle cx="100" cy="100" r="80" fill="none" stroke="#C2C9FA"
                                                stroke-width="40" />
                                            <circle cx="100" cy="100" r="80" fill="none" stroke="#94A1EE"
                                                stroke-width="40" stroke-dasharray="301.59 502.65"
                                                transform="rotate(-90 100 100)" />
                                            <text x="100" y="95" text-anchor="middle" font-size="32" font-weight="600"
                                                fill="#1a1a1a">60%</text>
                                            <text x="100" y="120" text-anchor="middle" font-size="14"
                                                fill="#999">40%</text>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/dashScript.js"></script>
</body>

</html>
