<style>
  .main-sidebar {
    background-color: #fffde7;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
  }

  .sidebar a.nav-link {
    color: #5c5c5c;
    font-size: 14px;
    margin: 3px 6px;
    border-radius: 6px;
    padding: 8px 12px;
    transition: all 0.3s ease;
  }

  .sidebar a.nav-link i.nav-icon {
    color: #219ebc;
    font-size: 16px;
    margin-right: 10px;
    transition: color 0.3s ease;
  }

  .sidebar a.nav-link:hover {
    background-color: #fff3cd;
    color: #ff6f00;
    font-weight: 500;
    transform: translateX(5px);
  }

  .sidebar a.nav-link.active {
    background-color: #8ecae6 !important;
    color: #fff !important;
    font-weight: bold;
    transform: scale(1.05);
  }

  .sidebar .nav-header {
    
    font-weight: 600;
    padding-left: 12px;
    font-size: 13px;
    margin-top: 10px;
    transition: all 0.3s ease;
  }

  .brand-link {
    background: linear-gradient(45deg,#219ebc,rgb(75, 40, 202));
    color: #fff !important;
    display: flex;
    align-items: center;
    padding: 10px 15px;
    transition: all 0.3s ease;
  }

  .brand-link .brand-text {
    font-size: 16px;
    font-weight: bold;
    margin-left: 10px;
    color: #fff !important;
  }

  .brand-image {
    border-radius: 50%;
    width: 30px;
    height: 30px;
    object-fit: cover;
  }
</style>


<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-light-warning elevation-2 sidebar-no-expand">
  <a href="<?php echo ($_settings->userdata('type') == 1) ? base_url . 'admin' : 'javascript:void(0);' ?>" class="brand-link">
    <img src="<?php echo validate_image($_settings->info('logo')) ?>" alt="Store Logo" class="brand-image">
    <span class="brand-text"><?php echo $_settings->info('short_name') ?></span>
  </a>

  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column text-sm nav-compact nav-flat nav-child-indent nav-collapse-hide-child" data-widget="treeview" role="menu" data-accordion="false">

        <!-- Dashboard (Only admin can access) -->
        <?php if($_settings->userdata('type') == 1): ?>
        <li class="nav-item dropdown">
          <a href="./" class="nav-link nav-home">
            <i class="nav-icon fas fa-chart-line"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <?php endif; ?>

        <!-- POS (cashier and admin only) -->
        <?php if($_settings->userdata('type') != 2): ?>
          
        <li class="nav-item dropdown">
    <a href="<?php echo base_url ?>admin/?page=table_management" class="nav-link nav-table_management">
        <i class="nav-icon fas fa-table"></i>
        <p>Table Management</p>
    </a>
</li>
        <li class="nav-item dropdown">
          <a href="./?page=sales/manage_sale" class="nav-link nav-sales_manage_sale">
            <i class="nav-icon fas fa-receipt"></i>
            <p>POS</p>
          </a>
        </li>
                <li class="nav-item dropdown">
          <a href="./?page=orders" class="nav-link nav-orders">
            <i class="nav-icon fas fa-clipboard-list"></i>
            <p>Order List</p>
          </a>
        </li>
        <?php endif; ?>

        <!-- Kitchen Side (kitchen and admin only) -->
        <?php if($_settings->userdata('type') != 3): ?>
        <li class="nav-item dropdown">
          <a href="./?page=kitchen" class="nav-link nav-kitchen">
            <i class="nav-icon fas fa-utensils"></i>
            <p>Kitchen Side</p>
          </a>
        </li>
        <?php endif; ?>

        <!-- Admin-only pages -->
        <?php if($_settings->userdata('type') == 1): ?>

        <!-- Order List -->



        <!-- Master List -->
        <li class="nav-header">Master List</li>

        <li class="nav-item dropdown">
          <a href="./?page=categories" class="nav-link nav-categories">
            <i class="nav-icon fas fa-layer-group"></i>
            <p>Category List</p>
          </a>
        </li>

        <li class="nav-item dropdown">
          <a href="./?page=menus" class="nav-link nav-menus">
            <i class="nav-icon fas fa-utensils"></i>
            <p>Menu List</p>
          </a>
        </li>

        <li class="nav-item dropdown">
          <a href="./?page=discount" class="nav-link nav-discounts">
            <i class="nav-icon fas fa-percentage"></i>
            <p>Discount List</p>
          </a>
        </li>

        <!-- Reports -->
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-chart-bar"></i>
            <p>
              Reports
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview" style="display: none;">
            <li class="nav-item">
              <a href="./?page=reports" class="nav-link tree-item nav-reports">
                <i class="fas fa-calendar-day nav-icon"></i>
                <p>Sales Report</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="./?page=reports/product" class="nav-link tree-item nav-product_reports">
                <i class="fas fa-box-open nav-icon"></i>
                <p>Product Sales Report</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- Maintenance -->
        <li class="nav-header">Maintenance</li>

        <li class="nav-item dropdown">
          <a href="<?php echo base_url ?>admin/?page=user/list" class="nav-link nav-user_list">
            <i class="nav-icon fas fa-users-cog"></i>
            <p>User List</p>
          </a>
        </li>

        <?php endif; ?>

      </ul>
    </nav>
  </div>
</aside>

<!-- Sidebar Highlight Script -->
<script>
  $(document).ready(function () {
    var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
    page = page.replace(/\//g, '_');
    
    // Highlight the correct nav link for the current page
    if ($('.nav-link.nav-' + page).length > 0) {
      $('.nav-link.nav-' + page).addClass('active');
      if ($('.nav-link.nav-' + page).hasClass('tree-item')) {
        $('.nav-link.nav-' + page).closest('.nav-treeview').parent().addClass('menu-open');
      }
      if ($('.nav-link.nav-' + page).hasClass('nav-is-tree')) {
        $('.nav-link.nav-' + page).parent().addClass('menu-open');
      }
    }
    
    // Special pages
    if (page === 'discount') {
      $('.nav-link.nav-discounts').addClass('active');
    }
    if (page === 'reports_product') {
      $('.nav-link.nav-product_reports').addClass('active');
    }

    $('.main-sidebar .nav-link').each(function () {
      var text = $(this).text();
      $(this).attr('title', text.trim());
    });

    $('.main-sidebar').on('mouseenter', function() {
      $(this).css('transition', 'all 0.3s ease');
    });

    $('.main-sidebar').on('mouseleave', function() {
      $(this).css('transition', 'all 0.3s ease');
    });
  });
</script>
