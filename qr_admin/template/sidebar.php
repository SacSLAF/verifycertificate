<div class="sidebar" data-background-color="dark">
  <div class="sidebar-logo">
    <!-- Logo Header -->
    <div class="logo-header" data-background-color="dark">
      <a href="index.php" class="logo">
        <img
          src="assets/img/kaiadmin/logo.png"
          alt="navbar brand"
          class="navbar-brand"
          height="30" />
      </a>
      <div class="nav-toggle">
        <button class="btn btn-toggle toggle-sidebar">
          <i class="gg-menu-right"></i>
        </button>
        <button class="btn btn-toggle sidenav-toggler">
          <i class="gg-menu-left"></i>
        </button>
      </div>
      <button class="topbar-toggler more">
        <i class="gg-more-vertical-alt"></i>
      </button>
    </div>
    <!-- End Logo Header -->
  </div>
  <div class="sidebar-wrapper scrollbar scrollbar-inner" style="scrollbar-width: none;">
    <div class="sidebar-content">
      <ul class="nav nav-secondary">
        <li class="nav-item active">
          <a href="dashboard.php"
            class="collapsed"
            aria-expanded="false">
            <i class="fas fa-home"></i>
            <p>Dashboard</p>

          </a>

        </li>
        <li class="nav-section">
          <span class="sidebar-mini-icon">
            <i class="fa fa-ellipsis-h"></i>
          </span>
          <h4 class="text-section">Components</h4>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#certifications">
            <i class="fas fa-layer-group"></i>
            <p>Certificates</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="certifications">
            <ul class="nav nav-collapse">
              <li>
                <a href="all-certificates.php">
                  <span class="sub-item">All certificates</span>
                </a>
              </li>
              <li>
                <a href="make-certificate.php?action=add">
                  <span class="sub-item">Add certificates</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#user">
            <i class="fas fa-layer-group"></i>
            <p>Users</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="user">
            <ul class="nav nav-collapse">
              <li>
                <a href="user.php">
                  <span class="sub-item">User</span>
                </a>
              </li>
              <?php if ($_SESSION["role"] == 'super_admin') { ?>
                <li>
                  <a href="create-users.php">
                    <span class="sub-item">Create users</span>
                  </a>
                </li>
              <?php } ?>

              <!-- <li>
                <a href="#">
                  <span class="sub-item">About</span>
                </a>
              </li> -->
            </ul>
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>

<?php
$directorates = array(
  0 => "All directorates",
  1 => "Directorate of Logistics",
  2 => "Directorate of Health Services",
  6 => "Directorate of Administration",
  3 => "Directorate of Air Operations",
  5 => "Directorate of Electronics and Computer Engineering",
  4 => "Directorate of Civil Engineering",
  7 => "Flight Safety Inspectorate",
  8 => "Directorate of Training",
  10 => "Basic Trade Course TTS Ekala - Directorate of Training",
  11 => "Advanced Trade Course TTS Ekala - Directorate of Training"
);

$directorate_id = $_SESSION["directorate"];
$directorate_name = isset($directorates[$directorate_id]) ? $directorates[$directorate_id] : 'Unknown Directorate';
?>