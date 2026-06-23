<?php
setlocale(LC_TIME, 'id_ID');
?>

<header class="top-header">

    <div class="header-left">

        <button class="sidebar-toggle-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#mobileSidebar">

            ☰

        </button>

        <div class="header-date">
            <?= date('l, d F Y') ?>
        </div>
    </div>

</header>