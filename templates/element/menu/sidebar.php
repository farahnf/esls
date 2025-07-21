<?php
$c_name = $this->request->getParam('controller');
$a_name = $this->request->getParam('action');
$loggedIn = $this->Identity->isLoggedIn();
$isAdmin = $loggedIn && $this->Identity->get('user_group_id') == 1;
?>

<!-- Sidebar -->
<nav id="sidebar" class="bg-dark text-white shadow">
  <div class="sidebar-header pt-3 ps-3 mb-4">
    <b class="gradient-animate-small">
      <i class="fa-solid fa-code me-2"></i><?= h($system_abbr); ?>
    </b>
  </div>

  <ul class="list-unstyled components px-2">

  <?php if (!$loggedIn): ?>
    <li class="menu-item <?= $c_name == 'Users' && $a_name == 'login' ? 'active' : '' ?>">
      <?= $this->Html->link(__('<i class="menu-icon fa-solid fa-right-to-bracket"></i> Sign-in'), 
        ['controller' => 'Users', 'action' => 'login', 'prefix' => false], 
        ['class' => 'menu-link', 'escape' => false]) ?>
    </li>
  <?php endif; ?>

  <?php if ($loggedIn): ?>
    <!-- General -->
    <li class="menu-header fw-bold text-uppercase mt-4 mb-3">
      <span class="menu-header-text ps-4">General</span>
      <div class="tricolor_line mb-3"></div>
    </li>

    <li class="menu-item <?= $c_name == 'Dashboards' && $a_name == 'index' ? 'active' : '' ?>">
      <?= $this->Html->link(__('<i class="menu-icon fa-solid fa-gauge-high"></i> Dashboard'), 
        ['controller' => 'Dashboards', 'action' => 'index', 'prefix' => false], 
        ['class' => 'menu-link', 'escape' => false]) ?>
    </li>

    <?php if (!$isAdmin): ?>
      <li class="menu-item <?= $c_name == 'Leaves' && $a_name == 'index' ? 'active' : '' ?>">
        <?= $this->Html->link(__('<i class="menu-icon fa-solid fa-pen-to-square"></i> Leaves'), 
          ['controller' => 'Leaves', 'action' => 'index', 'prefix' => false], 
          ['class' => 'menu-link', 'escape' => false]) ?>
      </li>

      <li class="menu-item <?= $c_name == 'Schedules' && $a_name == 'index' ? 'active' : '' ?>">
        <?= $this->Html->link(__('<i class="menu-icon fa-solid fa-calendar-days"></i> Schedules'), 
          ['controller' => 'Schedules', 'action' => 'index', 'prefix' => false], 
          ['class' => 'menu-link', 'escape' => false]) ?>
      </li>
    <?php endif; ?>

    <li class="menu-item <?= $c_name == 'Users' && $a_name == 'profile' ? 'active' : '' ?>">
      <?= $this->Html->link(__('<i class="menu-icon fa-solid fa-user-tie"></i> Profile'), 
        ['controller' => 'Users', 'action' => 'profile', 'prefix' => false, $this->Identity->get('slug')], 
        ['class' => 'menu-link', 'escape' => false]) ?>
    </li>

    <!-- Support -->
    <li class="menu-header fw-bold text-uppercase mt-4 mb-3">
      <span class="menu-header-text ps-4">Support</span>
      <div class="tricolor_line mb-3"></div>
    </li>

    <li class="menu-item <?= $c_name == 'Faqs' ? 'active' : '' ?>">
      <?= $this->Html->link(__('<i class="menu-icon fa-regular fa-circle-question"></i> FAQ'), 
        ['controller' => 'Faqs', 'action' => 'index', 'prefix' => false], 
        ['class' => 'menu-link', 'escape' => false]) ?>
    </li>

    <li class="menu-item <?= $c_name == 'Contact' ? 'active' : '' ?>">
      <?= $this->Html->link(__('<i class="menu-icon fa-regular fa-message"></i> Contact Us'), 
        ['controller' => 'Contact', 'action' => 'index', 'prefix' => false], 
        ['class' => 'menu-link', 'escape' => false]) ?>
    </li>

    <?php if ($isAdmin): ?>
      <!-- Administrator -->
      <li class="menu-header fw-bold text-uppercase mt-4 mb-3">
        <span class="menu-header-text ps-4">Administrator</span>
        <div class="tricolor_line mb-3"></div>
      </li>

      <li class="menu-item <?= $c_name == 'Employees' ? 'active' : '' ?>">
        <?= $this->Html->link(__('<i class="menu-icon fa-solid fa-id-card"></i> Employees'), 
          ['prefix' => 'Admin', 'controller' => 'Employees', 'action' => 'index'], 
          ['class' => 'menu-link', 'escape' => false]) ?>
      </li>

      <li class="menu-item <?= $c_name == 'Leaves' && $this->request->getParam('prefix') == 'Admin' ? 'active' : '' ?>">
        <?= $this->Html->link('<i class="menu-icon fa-solid fa-thumbs-up"></i> Leave Approvals', 
          ['prefix' => 'Admin', 'controller' => 'Leaves', 'action' => 'index'], 
          ['class' => 'menu-link', 'escape' => false]) ?>
      </li>

      <li class="menu-item <?= $c_name == 'Schedules' && $a_name == 'add' && $this->request->getParam('prefix') == 'Admin' ? 'active' : '' ?>">
        <?= $this->Html->link(__('<i class="menu-icon fa-solid fa-calendar-check"></i> Manage Schedules'), 
          ['prefix' => 'Admin', 'controller' => 'Schedules', 'action' => 'add'], 
          ['class' => 'menu-link', 'escape' => false]) ?>
      </li>

      <li class="menu-item <?= $c_name == 'Schedules' && $a_name == 'index' && $this->request->getParam('prefix') == 'Admin' ? 'active' : '' ?>">
        <?= $this->Html->link(__('<i class="menu-icon fa-solid fa-calendar"></i> Schedules List'), 
          ['prefix' => 'Admin', 'controller' => 'Schedules', 'action' => 'index'], 
          ['class' => 'menu-link', 'escape' => false]) ?>
      </li>

      <li class="menu-item <?= $c_name == 'Users' && $a_name == 'index' ? 'active' : '' ?>">
        <?= $this->Html->link(__('<i class="menu-icon fa-solid fa-users-viewfinder"></i> User Management'), 
          ['prefix' => 'Admin', 'controller' => 'Users', 'action' => 'index'], 
          ['class' => 'menu-link', 'escape' => false]) ?>
      </li>

      <li class="menu-item <?= $c_name == 'Shifts' && $a_name == 'index' ? 'active' : '' ?>">
        <?= $this->Html->link(__('<i class="menu-icon fa-solid fa-briefcase-clock"></i> Shifts'), 
          ['prefix' => 'Admin', 'controller' => 'Shifts', 'action' => 'index'], 
          ['class' => 'menu-link', 'escape' => false]) ?>
      </li>

      <li class="menu-item <?= $c_name == 'PublicHolidays' && $a_name == 'index' ? 'active' : '' ?>">
        <?= $this->Html->link(__('<i class="menu-icon fa-solid fa-plane"></i> Public Holidays'), 
          ['prefix' => 'Admin', 'controller' => 'PublicHolidays', 'action' => 'index'], 
          ['class' => 'menu-link', 'escape' => false]) ?>
      </li>

      <li class="menu-item <?= $c_name == 'Settings' && $a_name == 'update' ? 'active' : '' ?>">
        <?= $this->Html->link(__('<i class="menu-icon fa-solid fa-gear"></i> Site Configuration'), 
          ['prefix' => 'Admin', 'controller' => 'Settings', 'action' => 'update', 'recrud'], 
          ['class' => 'menu-link', 'escape' => false]) ?>
      </li>
    <?php endif; ?>

  <?php endif; ?>

</ul>
</nav>
